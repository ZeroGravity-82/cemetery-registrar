<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\Passport;
use Cemetery\Registrar\Domain\Model\NaturalPerson\PlaceOfBirth;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonTest extends AggregateRootTest
{
    private NaturalPersonId $naturalPersonId;
    private FullName        $fullName;
    private NaturalPerson   $naturalPerson;
    
    public function setUp(): void
    {
        $this->naturalPersonId = new NaturalPersonId('777');
        $this->fullName        = new FullName('Иванов Иван Иванович');
        $this->naturalPerson   = new NaturalPerson($this->naturalPersonId, $this->fullName);
        $this->entity          = $this->naturalPerson;
    }

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('NATURAL_PERSON', NaturalPerson::CLASS_SHORTCUT);
    }

    public function testItHasValidClassLabelConstant(): void
    {
        $this->assertSame('физлицо', NaturalPerson::CLASS_LABEL);
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(NaturalPersonId::class, $this->naturalPerson->id());
        $this->assertTrue($this->naturalPerson->id()->isEqual($this->naturalPersonId));
        $this->assertInstanceOf(FullName::class, $this->naturalPerson->fullName());
        $this->assertTrue($this->naturalPerson->fullName()->isEqual($this->fullName));
        $this->assertNull($this->naturalPerson->phone());
        $this->assertNull($this->naturalPerson->phoneAdditional());
        $this->assertNull($this->naturalPerson->email());
        $this->assertNull($this->naturalPerson->address());
        $this->assertNull($this->naturalPerson->bornAt());
        $this->assertNull($this->naturalPerson->placeOfBirth());
    }

    public function testItSetsFullName(): void
    {
        $fullName = new FullName('Петров Пётр Петрович');
        $this->naturalPerson->setFullName($fullName);
        $this->assertInstanceOf(FullName::class, $this->naturalPerson->fullName());
        $this->assertTrue($this->naturalPerson->fullName()->isEqual($fullName));
    }

    public function testItSetsPhone(): void
    {
        $phone = new PhoneNumber('+7-913-777-88-99');
        $this->naturalPerson->setPhone($phone);
        $this->assertInstanceOf(PhoneNumber::class, $this->naturalPerson->phone());
        $this->assertTrue($this->naturalPerson->phone()->isEqual($phone));

        $this->naturalPerson->setPhone(null);
        $this->assertNull($this->naturalPerson->phone());
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phoneAdditional = new PhoneNumber('+7-913-555-66-77');
        $this->naturalPerson->setPhoneAdditional($phoneAdditional);
        $this->assertInstanceOf(PhoneNumber::class, $this->naturalPerson->phoneAdditional());
        $this->assertTrue($this->naturalPerson->phoneAdditional()->isEqual($phoneAdditional));

        $this->naturalPerson->setPhoneAdditional(null);
        $this->assertNull($this->naturalPerson->phoneAdditional());
    }

    public function testItSetsEmail(): void
    {
        $email = new Email('info@example.com');
        $this->naturalPerson->setEmail($email);
        $this->assertInstanceOf(Email::class, $this->naturalPerson->email());
        $this->assertTrue($this->naturalPerson->email()->isEqual($email));

        $this->naturalPerson->setEmail(null);
        $this->assertNull($this->naturalPerson->email());
    }

    public function testItSetsAddress(): void
    {
        $address = new Address('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37');
        $this->naturalPerson->setAddress($address);
        $this->assertInstanceOf(Address::class, $this->naturalPerson->address());
        $this->assertTrue($this->naturalPerson->address()->isEqual($address));

        $this->naturalPerson->setAddress(null);
        $this->assertNull($this->naturalPerson->address());
    }

    public function testItSetsBornAt(): void
    {
        $bornAt = new \DateTimeImmutable('2000-01-01');
        $this->naturalPerson->setBornAt($bornAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->bornAt());
        $this->assertSame('2000-01-01', $this->naturalPerson->bornAt()->format('Y-m-d'));

        $this->naturalPerson->setBornAt(null);
        $this->assertNull($this->naturalPerson->bornAt());
    }

    public function testItSetsPlaceOfBirth(): void
    {
        $placeOfBirth = new PlaceOfBirth('город Новосибирск');
        $this->naturalPerson->setPlaceOfBirth($placeOfBirth);
        $this->assertInstanceOf(PlaceOfBirth::class, $this->naturalPerson->placeOfBirth());
        $this->assertTrue($this->naturalPerson->placeOfBirth()->isEqual($placeOfBirth));

        $this->naturalPerson->setPlaceOfBirth(null);
        $this->assertNull($this->naturalPerson->placeOfBirth());
    }

    public function testItSetsPassport(): void
    {
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'УВД Кировского района города Новосибирска',
            '540-001',
        );
        $this->naturalPerson->setPassport($passport);
        $this->assertInstanceOf(Passport::class, $this->naturalPerson->passport());
        $this->assertTrue($this->naturalPerson->passport()->isEqual($passport));

        $this->naturalPerson->setPassport(null);
        $this->assertNull($this->naturalPerson->passport());
    }
}
