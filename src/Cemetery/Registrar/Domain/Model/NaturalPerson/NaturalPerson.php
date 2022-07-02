<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Domain\Model\NaturalPerson\Exception\NaturalPersonException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPerson extends AggregateRoot
{
    public const CLASS_SHORTCUT = 'NATURAL_PERSON';
    public const CLASS_LABEL    = 'физлицо';

    /**
     * @var PhoneNumber|null
     */
    private ?PhoneNumber $phone = null;

    /**
     * @var PhoneNumber|null
     */
    private ?PhoneNumber $phoneAdditional = null;

    /**
     * @var Email|null
     */
    private ?Email $email = null;

    /**
     * @var Address|null
     */
    private ?Address $address = null;

    /**
     * @var \DateTimeImmutable|null
     */
    private ?\DateTimeImmutable $bornAt = null;

    /**
     * @var PlaceOfBirth|null
     */
    private ?PlaceOfBirth $placeOfBirth = null;

    /**
     * @var Passport|null
     */
    private ?Passport $passport = null;

    /**
     * @var DeceasedDetails|null
     */
    private ?DeceasedDetails $deceasedDetails = null;

    /**
     * @param NaturalPersonId $id
     * @param FullName        $fullName
     */
    public function __construct(
        private readonly NaturalPersonId $id,
        private FullName                 $fullName,
    ) {
        parent::__construct();
    }

    /**
     * @return NaturalPersonId
     */
    public function id(): NaturalPersonId
    {
        return $this->id;
    }

    /**
     * @return FullName
     */
    public function fullName(): FullName
    {
        return $this->fullName;
    }

    /**
     * @param FullName $fullName
     *
     * @return $this
     */
    public function setFullName(FullName $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return PhoneNumber|null
     */
    public function phone(): ?PhoneNumber
    {
        return $this->phone;
    }

    /**
     * @param PhoneNumber|null $phone
     *
     * @return $this
     */
    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return PhoneNumber|null
     */
    public function phoneAdditional(): ?PhoneNumber
    {
        return $this->phoneAdditional;
    }

    /**
     * @param PhoneNumber|null $phoneAdditional
     *
     * @return $this
     */
    public function setPhoneAdditional(?PhoneNumber $phoneAdditional): self
    {
        $this->phoneAdditional = $phoneAdditional;

        return $this;
    }

    /**
     * @return Email|null
     */
    public function email(): ?Email
    {
        return $this->email;
    }

    /**
     * @param Email|null $email
     *
     * @return $this
     */
    public function setEmail(?Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Address|null
     */
    public function address(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address|null $address
     *
     * @return $this
     */
    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function bornAt(): ?\DateTimeImmutable
    {
        return $this->bornAt;
    }

    /**
     * @param \DateTimeImmutable|null $bornAt
     *
     * @return $this
     *
     * @throws NaturalPersonException when the birthdate follows the death date (if any)
     */
    public function setBornAt(?\DateTimeImmutable $bornAt): self
    {
        $this->assertValidBirthdate($bornAt);
        $this->bornAt = $bornAt;

        return $this;
    }

    /**
     * @return Passport|null
     */
    public function passport(): ?Passport
    {
        return $this->passport;
    }

    /**
     * @param Passport|null $passport
     *
     * @return $this
     */
    public function setPassport(?Passport $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    /**
     * @return PlaceOfBirth|null
     */
    public function placeOfBirth(): ?PlaceOfBirth
    {
        return $this->placeOfBirth;
    }

    /**
     * @param PlaceOfBirth|null $placeOfBirth
     *
     * @return $this
     */
    public function setPlaceOfBirth(?PlaceOfBirth $placeOfBirth): self
    {
        $this->placeOfBirth = $placeOfBirth;

        return $this;
    }

    /**
     * @return DeceasedDetails|null
     */
    public function deceasedDetails(): ?DeceasedDetails
    {
        return $this->deceasedDetails;
    }

    /**
     * @param DeceasedDetails|null $deceasedDetails
     *
     * @return $this
     *
     * @throws NaturalPersonException when the death date precedes the birthdate (if any)
     * @throws NaturalPersonException when the age is provided when both birthdate and death date are set
     */
    public function setDeceasedDetails(?DeceasedDetails $deceasedDetails): self
    {
        $this->assertValidDeceasedDetails($deceasedDetails);
        $this->deceasedDetails = $deceasedDetails;

        return $this;
    }

    /**
     * Checks that birthdate not follows the death date (if any).
     *
     * @param \DateTimeImmutable|null $bornAt
     *
     * @throws NaturalPersonException when the birthdate follows the death date (if any)
     */
    private function assertValidBirthdate(?\DateTimeImmutable $bornAt): void
    {
        if (!$bornAt || !$this->deceasedDetails()?->diedAt()) {
            return;
        }
        if ($bornAt > $this->deceasedDetails()->diedAt()) {
            throw NaturalPersonException::birthdateFollowsDeathDate();
        }
    }

    /**
     * Checks that the deceased details matches the rest of the data of the natural person.
     *
     * @param DeceasedDetails|null $deceasedDetails
     *
     * @throws NaturalPersonException when the death date precedes the birthdate (if any)
     * @throws NaturalPersonException when the age is provided when both birthdate and death date are set
     */
    private function assertValidDeceasedDetails(?DeceasedDetails $deceasedDetails): void
    {
        $this->assertValidDeathDate($deceasedDetails);
        $this->assertAgeIsNotRedundant($deceasedDetails);
    }

    /**
     * Checks that the death date from the deceased details not precedes the birthdate (if any).
     *
     * @param DeceasedDetails|null $deceasedDetails
     *
     * @throws NaturalPersonException when the death date precedes the birthdate (if any)
     */
    private function assertValidDeathDate(?DeceasedDetails $deceasedDetails): void
    {
        if (!$deceasedDetails?->diedAt() || !$this->bornAt()) {
            return;
        }
        if ($deceasedDetails->diedAt() < $this->bornAt()) {
            throw NaturalPersonException::deathDatePrecedesBirthdate();
        }
    }

    /**
     * Checks that the age from the deceased details is not provided when both birthdate and death date are set.
     *
     * @param DeceasedDetails|null $deceasedDetails
     *
     * @throws NaturalPersonException when the age is provided when both birthdate and death date are set
     */
    private function assertAgeIsNotRedundant(?DeceasedDetails $deceasedDetails): void
    {
        if (!$deceasedDetails?->diedAt() || !$this->bornAt()) {
            return;
        }
        if ($deceasedDetails?->age()) {
            throw NaturalPersonException::ageForBothBirthAndDeathDatesSet();
        }
    }
}
