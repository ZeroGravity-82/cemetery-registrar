<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\IdentityGenerator;
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
    private MockObject|IdentityGenerator $mockIdentityGenerator;
    private NaturalPersonBuilder         $naturalPersonBuilder;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGenerator::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

        $this->naturalPersonBuilder = new NaturalPersonBuilder($this->mockIdentityGenerator);
        $this->naturalPersonBuilder->initialize('Иванов Иван Иванович');
    }

    public function testItInitializesANaturalPersonWithRequiredFields(): void
    {
        $naturalPerson = $this->naturalPersonBuilder->build();

        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertInstanceOf(NaturalPersonId::class, $naturalPerson->id());
        $this->assertSame('555', (string) $naturalPerson->id());
        $this->assertInstanceOf(FullName::class, $naturalPerson->fullName());
        $this->assertSame('Иванов Иван Иванович', (string) $naturalPerson->fullName());
        $this->assertNull($naturalPerson->phone());
        $this->assertNull($naturalPerson->phoneAdditional());
        $this->assertNull($naturalPerson->email());
        $this->assertNull($naturalPerson->address());
        $this->assertNull($naturalPerson->bornAt());
        $this->assertNull($naturalPerson->placeOfBirth());
        $this->assertNull($naturalPerson->passport());
    }

    public function testItFailsToBuildANaturalPersonBeforeInitialization(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf('Строитель для класса %s не инициализирован.', NaturalPerson::class));

        $naturalPersonBuilder = new NaturalPersonBuilder($this->mockIdentityGenerator);
        $naturalPersonBuilder->build();
    }

    public function testItAddsAPhone(): void
    {
        $phone         = '+7-999-555-44-33';
        $naturalPerson = $this->naturalPersonBuilder->addPhone($phone)->build();
        $this->assertSame('+7-999-555-44-33', $naturalPerson->phone());
    }

    public function testItAddsAPhoneAdditional(): void
    {
        $phoneAdditional = '+7-999-777-11-22';
        $naturalPerson   = $this->naturalPersonBuilder->addPhoneAdditional($phoneAdditional)->build();
        $this->assertSame('+7-999-777-11-22', $naturalPerson->phoneAdditional());
    }

    public function testItAddsAnEmail(): void
    {
        $email         = 'info@example.com';
        $naturalPerson = $this->naturalPersonBuilder->addEmail($email)->build();
        $this->assertSame('info@example.com', $naturalPerson->email());
    }

    public function testItAddsAnAddress(): void
    {
        $address       = 'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37';
        $naturalPerson = $this->naturalPersonBuilder->addAddress($address)->build();
        $this->assertSame('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37', $naturalPerson->address());
    }

    public function testItAddsABornAt(): void
    {
        $bornAt        = new \DateTimeImmutable('1972-02-10');
        $naturalPerson = $this->naturalPersonBuilder->addBornAt($bornAt)->build();
        $this->assertInstanceOf(\DateTimeImmutable::class, $naturalPerson->bornAt());
        $this->assertSame('1972-02-10', $naturalPerson->bornAt()->format('Y-m-d'));
    }

    public function testItAddsAPlaceOfBirth(): void
    {
        $placeOfBirth  = 'город Новосибирск';
        $naturalPerson = $this->naturalPersonBuilder->addPlaceOfBirth($placeOfBirth)->build();
        $this->assertSame('город Новосибирск', $naturalPerson->placeOfBirth());
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
        $this->assertInstanceOf(Passport::class, $naturalPerson->passport());
        $this->assertSame('1234', $naturalPerson->passport()->series());
        $this->assertSame('567890', $naturalPerson->passport()->number());
        $this->assertSame('2001-01-01', $naturalPerson->passport()->issuedAt()->format('Y-m-d'));
        $this->assertSame('УВД Кировского района города Новосибирска', $naturalPerson->passport()->issuedBy());
        $this->assertSame('540-001', $naturalPerson->passport()->divisionCode());
    }
}
