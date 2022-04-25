<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonBuilder;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\Passport;
use Cemetery\Registrar\Domain\NaturalPerson\PlaceOfBirth;
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
        $phone         = new PhoneNumber('+7-999-555-44-33');
        $naturalPerson = $this->naturalPersonBuilder->addPhone($phone)->build();
        $this->assertInstanceOf(PhoneNumber::class, $naturalPerson->phone());
        $this->assertTrue($naturalPerson->phone()->isEqual($phone));
    }

    public function testItAddsAPhoneAdditional(): void
    {
        $phoneAdditional = new PhoneNumber('+7-999-777-11-22');
        $naturalPerson   = $this->naturalPersonBuilder->addPhoneAdditional($phoneAdditional)->build();
        $this->assertInstanceOf(PhoneNumber::class, $naturalPerson->phoneAdditional());
        $this->assertTrue($naturalPerson->phoneAdditional()->isEqual($phoneAdditional));
    }

    public function testItAddsAnEmail(): void
    {
        $email         = new Email('info@example.com');
        $naturalPerson = $this->naturalPersonBuilder->addEmail($email)->build();
        $this->assertInstanceOf(Email::class, $naturalPerson->email());
        $this->assertTrue($naturalPerson->email()->isEqual($email));
    }

    public function testItAddsAnAddress(): void
    {
        $address       = new Address('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37');
        $naturalPerson = $this->naturalPersonBuilder->addAddress($address)->build();
        $this->assertInstanceOf(Address::class, $naturalPerson->address());
        $this->assertTrue($naturalPerson->address()->isEqual($address));
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
        $placeOfBirth  = new PlaceOfBirth('город Новосибирск');
        $naturalPerson = $this->naturalPersonBuilder->addPlaceOfBirth($placeOfBirth)->build();
        $this->assertInstanceOf(PlaceOfBirth::class, $naturalPerson->placeOfBirth());
        $this->assertTrue($naturalPerson->placeOfBirth()->isEqual($placeOfBirth));
    }

    public function testItAddsPassport(): void
    {
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'УВД Кировского района города Новосибирска',
            '540-001',
        );
        $naturalPerson = $this->naturalPersonBuilder
            ->addPassport(
                $passport->series(),
                $passport->number(),
                $passport->issuedAt(),
                $passport->issuedBy(),
                $passport->divisionCode(),
            )
            ->build();
        $this->assertInstanceOf(Passport::class, $naturalPerson->passport());
        $this->assertTrue($naturalPerson->passport()->isEqual($passport));
    }
}
