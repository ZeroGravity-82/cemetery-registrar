<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Domain\Model\NaturalPerson\Exception\NaturalPersonException;
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
        $this->assertNull($this->naturalPerson->passport());
        $this->assertNull($this->naturalPerson->deceasedDetails());
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

    public function testItSetsDeceasedDetails(): void
    {
        $deceasedDetails = new DeceasedDetails(
            new \DateTimeImmutable('2011-04-30'),
            new Age(82),
            new CauseOfDeathId('CD001'),
            new DeathCertificate('V-МЮ', '532515', new \DateTimeImmutable('2002-10-28')),
            new CremationCertificate('12964', new \DateTimeImmutable('2002-10-29')),
        );
        $this->naturalPerson->setDeceasedDetails($deceasedDetails);
        $this->assertInstanceOf(DeceasedDetails::class, $this->naturalPerson->deceasedDetails());
        $this->assertTrue($this->naturalPerson->deceasedDetails()->isEqual($deceasedDetails));

        $this->naturalPerson->setDeceasedDetails(null);
        $this->assertNull($this->naturalPerson->deceasedDetails());
    }

    // ------------------------- "bornAt <-> deceasedDetails->diedAt" invariant testing --------------------------

    public function testItFailsWhenSettingBirthdateFollowsDeathDate(): void
    {
        // Prepare entity for testing
        $deceasedDetails = new DeceasedDetails(
            new \DateTimeImmutable('2001-05-13'),
            null,
            null,
            null,
            null,
        );
        $this->naturalPerson->setDeceasedDetails($deceasedDetails);

        // Testing itself
        $this->expectException(NaturalPersonException::class);
        $this->expectExceptionMessage(NaturalPersonException::BIRTHDATE_FOLLOWS_DEATH_DATE);
        $this->naturalPerson->setBornAt(new \DateTimeImmutable('2010-05-13'));
    }

    public function testItFailsWhenSettingDeathDatePrecedesBirthdate(): void
    {
        // Prepare entity for testing
        $this->naturalPerson->setBornAt(new \DateTimeImmutable('2010-05-13'));

        // Testing itself
        $this->expectException(NaturalPersonException::class);
        $this->expectExceptionMessage(NaturalPersonException::DEATH_DATE_PRECEDES_BIRTHDATE);
        $deceasedDetails = new DeceasedDetails(
            new \DateTimeImmutable('2001-05-13'),
            null,
            null,
            null,
            null,
        );
        $this->naturalPerson->setDeceasedDetails($deceasedDetails);
    }

    public function testItFailsWhenSettingAgeForBothBirthAndDeathDatesSet(): void
    {
        // Prepare entity for testing
        $this->naturalPerson->setBornAt(new \DateTimeImmutable('2001-05-13'));

        // Testing itself
        $this->expectException(NaturalPersonException::class);
        $this->expectExceptionMessage(NaturalPersonException::AGE_FOR_BOTH_BIRTH_AND_DEATH_DATES_SET);
        $deceasedDetails = new DeceasedDetails(
            new \DateTimeImmutable('2010-05-13'),
            new Age(9),
            null,
            null,
            null,
        );
        $this->naturalPerson->setDeceasedDetails($deceasedDetails);
    }
}
