<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonFactoryTest extends TestCase
{
    private MockObject|IdentityGenerator $mockIdentityGenerator;
    private NaturalPersonFactory         $naturalPersonFactory;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGenerator::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $this->naturalPersonFactory = new NaturalPersonFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesNaturalPersonForDeceased(): void
    {
        $fullName = 'Иванов Иван Иванович';
        $bornAt   = '1940-05-10';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $naturalPerson = $this->naturalPersonFactory->createForDeceased($fullName, $bornAt);
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertSame('555', $naturalPerson->id()->value());
        $this->assertSame($fullName, $naturalPerson->fullName()->value());
        $this->assertSame($bornAt, $naturalPerson->bornAt()->format('Y-m-d'));
    }

    public function testItCreatesNaturalPersonForDeceasedWithoutOptionalFields(): void
    {
        $fullName = 'Иванов Иван Иванович';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $naturalPerson = $this->naturalPersonFactory->createForDeceased($fullName, null);
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertSame('555', $naturalPerson->id()->value());
        $this->assertSame($fullName, $naturalPerson->fullName()->value());
        $this->assertNull($naturalPerson->bornAt());
    }

    public function testItCreatesNaturalPersonForCustomer(): void
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
        $naturalPerson = $this->naturalPersonFactory->createForBurialCustomer(
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
        $this->assertSame('555', $naturalPerson->id()->value());
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

    public function testItCreatesNaturalPersonForCustomerWithoutOptionalFields(): void
    {
        $fullName = 'Иванов Иван Иванович';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $naturalPerson = $this->naturalPersonFactory->createForBurialCustomer(
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
        $this->assertSame('555', $naturalPerson->id()->value());
        $this->assertSame($fullName, $naturalPerson->fullName()->value());
        $this->assertNull($naturalPerson->phone());
        $this->assertNull($naturalPerson->phoneAdditional());
        $this->assertNull($naturalPerson->email());
        $this->assertNull($naturalPerson->address());
        $this->assertNull($naturalPerson->bornAt());
        $this->assertNull($naturalPerson->placeOfBirth());
        $this->assertNull($naturalPerson->passport());
    }
}
