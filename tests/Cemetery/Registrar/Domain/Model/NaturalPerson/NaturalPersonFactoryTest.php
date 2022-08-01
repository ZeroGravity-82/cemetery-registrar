<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonFactory;
use Cemetery\Tests\Registrar\Domain\Model\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonFactoryTest extends EntityFactoryTest
{
    private NaturalPersonFactory $naturalPersonFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->naturalPersonFactory = new NaturalPersonFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesNaturalPerson(): void
    {
        $fullName             = 'Иванов Иван Иванович';
        $phone                = '+7-913-777-88-99';
        $phoneAdditional      = '8(383)123-45-67';
        $email                = 'info@google.com';
        $address              = 'г. Новосибирск, ул. 3 Интернационала, д. 127';
        $bornAt               = '1940-05-10';
        $placeOfBirth         = 'г. Новосибирск';
        $passportSeries       = '1234';
        $passportNumber       = '567890';
        $passportIssuedAt     = '2002-10-28';
        $passportIssuedBy     = 'УВД Кировского района города Новосибирска';
        $passportDivisionCode = '540-001';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $naturalPerson = $this->naturalPersonFactory->create(
            $fullName,
            $phone,
            $phoneAdditional,
            $email,
            $address,
            $bornAt,
            $placeOfBirth,
            $passportSeries,
            $passportNumber,
            $passportIssuedAt,
            $passportIssuedBy,
            $passportDivisionCode,
        );
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertSame(self::ENTITY_ID, $naturalPerson->id()->value());
        $this->assertSame($fullName, $naturalPerson->fullName()->value());
        $this->assertSame($phone, $naturalPerson->phone()->value());
        $this->assertSame($phoneAdditional, $naturalPerson->phoneAdditional()->value());
        $this->assertSame($email, $naturalPerson->email()->value());
        $this->assertSame($address, $naturalPerson->address()->value());
        $this->assertSame($bornAt, $naturalPerson->bornAt()->format('Y-m-d'));
        $this->assertSame($placeOfBirth, $naturalPerson->placeOfBirth()->value());
        $this->assertSame($passportSeries, $naturalPerson->passport()->series());
        $this->assertSame($passportNumber, $naturalPerson->passport()->number());
        $this->assertSame($passportIssuedAt, $naturalPerson->passport()->issuedAt()->format('Y-m-d'));
        $this->assertSame($passportIssuedBy, $naturalPerson->passport()->issuedBy());
        $this->assertSame($passportDivisionCode, $naturalPerson->passport()->divisionCode());
    }

    public function testItCreatesNaturalPersonWithoutOptionalFields(): void
    {
        $fullName = 'Иванов Иван Иванович';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $naturalPerson = $this->naturalPersonFactory->create(
            $fullName,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertSame(self::ENTITY_ID, $naturalPerson->id()->value());
        $this->assertSame($fullName, $naturalPerson->fullName()->value());
        $this->assertNull($naturalPerson->phone());
        $this->assertNull($naturalPerson->phoneAdditional());
        $this->assertNull($naturalPerson->email());
        $this->assertNull($naturalPerson->address());
        $this->assertNull($naturalPerson->bornAt());
        $this->assertNull($naturalPerson->placeOfBirth());
        $this->assertNull($naturalPerson->passport());
    }

    public function testItFailsWithInvalidBornAtDateFormat(): void
    {
        $this->markTestIncomplete();
    }

    public function testItFailsWithInvalidPassportIssuedAtDateFormat(): void
    {
        $this->markTestIncomplete();
    }
}
