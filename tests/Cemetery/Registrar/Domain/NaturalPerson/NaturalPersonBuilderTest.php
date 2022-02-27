<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonBuilder;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\Passport;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonBuilderTest extends TestCase
{
    private MockObject|IdentityGeneratorInterface $mockIdentityGenerator;
    private NaturalPersonBuilder                  $naturalPersonBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGeneratorInterface::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $this->naturalPersonBuilder = new NaturalPersonBuilder($this->mockIdentityGenerator);
        $this->naturalPersonBuilder->initialize('Иванов Иван Иванович');
    }

    public function testItInitializesANaturalPersonWithRequiredFields(): void
    {
        $naturalPerson = $this->naturalPersonBuilder->build();

        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertInstanceOf(NaturalPersonId::class, $naturalPerson->getId());
        $this->assertSame('555', (string) $naturalPerson->getId());
        $this->assertInstanceOf(FullName::class, $naturalPerson->getFullName());
        $this->assertSame('Иванов Иван Иванович', (string) $naturalPerson->getFullName());
        $this->assertNull($naturalPerson->getPhone());
        $this->assertNull($naturalPerson->getPhoneAdditional());
        $this->assertNull($naturalPerson->getEmail());
        $this->assertNull($naturalPerson->getAddress());
        $this->assertNull($naturalPerson->getBornAt());
        $this->assertNull($naturalPerson->getPlaceOfBirth());
        $this->assertNull($naturalPerson->getPassport());
    }

    public function testItFailsToBuildANaturalPersonBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Строитель физического лица не инициализирован.');

        $naturalPersonBuilder = new NaturalPersonBuilder($this->mockIdentityGenerator);
        $naturalPersonBuilder->build();
    }

    public function testItAddsAPhone(): void
    {
        $phone         = '+7-999-555-44-33';
        $naturalPerson = $this->naturalPersonBuilder->addPhone($phone)->build();
        $this->assertSame('+7-999-555-44-33', $naturalPerson->getPhone());
    }

    public function testItAddsAPhoneAdditional(): void
    {
        $phoneAdditional = '+7-999-777-11-22';
        $naturalPerson   = $this->naturalPersonBuilder->addPhoneAdditional($phoneAdditional)->build();
        $this->assertSame('+7-999-777-11-22', $naturalPerson->getPhoneAdditional());
    }

    public function testItAddsAnEmail(): void
    {
        $email         = 'info@example.com';
        $naturalPerson = $this->naturalPersonBuilder->addEmail($email)->build();
        $this->assertSame('info@example.com', $naturalPerson->getEmail());
    }

    public function testItAddsAnAddress(): void
    {
        $address       = 'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37';
        $naturalPerson = $this->naturalPersonBuilder->addAddress($address)->build();
        $this->assertSame('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37', $naturalPerson->getAddress());
    }

    public function testItAddsABornAt(): void
    {
        $bornAt        = new \DateTimeImmutable('1972-02-10');
        $naturalPerson = $this->naturalPersonBuilder->addBornAt($bornAt)->build();
        $this->assertInstanceOf(\DateTimeImmutable::class, $naturalPerson->getBornAt());
        $this->assertSame('1972-02-10', $naturalPerson->getBornAt()->format('Y-m-d'));
    }

    public function testItAddsAPlaceOfBirth(): void
    {
        $placeOfBirth  = 'город Новосибирск';
        $naturalPerson = $this->naturalPersonBuilder->addPlaceOfBirth($placeOfBirth)->build();
        $this->assertSame('город Новосибирск', $naturalPerson->getPlaceOfBirth());
    }

    public function testItAddsPassport(): void
    {
        $passportSeries       = '1234';
        $passportNumber       = '567890';
        $passportIssuedAt     = new \DateTimeImmutable('2001-01-01');
        $passportIssuedBy     = 'УВД Кировского района города Новосибирска';
        $passportDivisionCode = '540-001';
        $naturalPerson        = $this->naturalPersonBuilder
            ->addPassport(
                $passportSeries,
                $passportNumber,
                $passportIssuedAt,
                $passportIssuedBy,
                $passportDivisionCode,
            )
            ->build();
        $this->assertInstanceOf(Passport::class, $naturalPerson->getPassport());
        $this->assertSame('1234', $naturalPerson->getPassport()->getSeries());
        $this->assertSame('567890', $naturalPerson->getPassport()->getNumber());
        $this->assertSame('2001-01-01', $naturalPerson->getPassport()->getIssuedAt()->format('Y-m-d'));
        $this->assertSame('УВД Кировского района города Новосибирска', $naturalPerson->getPassport()->getIssuedBy());
        $this->assertSame('540-001', $naturalPerson->getPassport()->getDivisionCode());
    }

    public function testItIgnoresNullValues(): void
    {
        $naturalPerson = $this->naturalPersonBuilder
            ->addPhone(null)
            ->addPhoneAdditional(null)
            ->addEmail(null)
            ->addAddress(null)
            ->addBornAt(null)
            ->addPlaceOfBirth(null)
            ->addPassport(null, null, null, null, null)
            ->build();
        $this->assertNull($naturalPerson->getPhone());
        $this->assertNull($naturalPerson->getPhoneAdditional());
        $this->assertNull($naturalPerson->getEmail());
        $this->assertNull($naturalPerson->getAddress());
        $this->assertNull($naturalPerson->getBornAt());
        $this->assertNull($naturalPerson->getPlaceOfBirth());
        $this->assertNull($naturalPerson->getPassport());
    }
}
