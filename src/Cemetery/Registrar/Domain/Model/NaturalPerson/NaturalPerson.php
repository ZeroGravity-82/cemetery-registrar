<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPerson extends AggregateRoot
{
    public const CLASS_SHORTCUT = 'NATURAL_PERSON';
    public const CLASS_LABEL    = 'физлицо';

    private ?PhoneNumber        $phone           = null;
    private ?PhoneNumber        $phoneAdditional = null;
    private ?Address            $address         = null;
    private ?Email              $email           = null;
    private ?\DateTimeImmutable $bornAt          = null;
    private ?PlaceOfBirth       $placeOfBirth    = null;
    private ?Passport           $passport        = null;
    private ?DeceasedDetails    $deceasedDetails = null;

    public function __construct(
        private NaturalPersonId $id,
        private FullName        $fullName,
    ) {
        parent::__construct();
    }

    public function id(): NaturalPersonId
    {
        return $this->id;
    }

    public function fullName(): FullName
    {
        return $this->fullName;
    }

    public function setFullName(FullName $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function phone(): ?PhoneNumber
    {
        return $this->phone;
    }

    public function setPhone(?PhoneNumber $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function phoneAdditional(): ?PhoneNumber
    {
        return $this->phoneAdditional;
    }

    public function setPhoneAdditional(?PhoneNumber $phoneAdditional): self
    {
        $this->phoneAdditional = $phoneAdditional;

        return $this;
    }

    public function address(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function email(): ?Email
    {
        return $this->email;
    }

    public function setEmail(?Email $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function bornAt(): ?\DateTimeImmutable
    {
        return $this->bornAt;
    }

    /**
     * @throws Exception when the birthdate follows the death date (if any)
     * @throws Exception when birthdate and death date do not match age (if any)
     */
    public function setBornAt(?\DateTimeImmutable $bornAt): self
    {
        $this->assertBornAtNotFollowsDiedAt($bornAt, $this->deceasedDetails());
        $this->assertBornAtAndDiedAtAreMatchingAge($bornAt, $this->deceasedDetails());
        $this->bornAt = $bornAt;
        $this->clearAgeIfItIsRedundant();

        return $this;
    }

    public function passport(): ?Passport
    {
        return $this->passport;
    }

    public function setPassport(?Passport $passport): self
    {
        $this->passport = $passport;

        return $this;
    }

    public function placeOfBirth(): ?PlaceOfBirth
    {
        return $this->placeOfBirth;
    }

    public function setPlaceOfBirth(?PlaceOfBirth $placeOfBirth): self
    {
        $this->placeOfBirth = $placeOfBirth;

        return $this;
    }

    public function deceasedDetails(): ?DeceasedDetails
    {
        return $this->deceasedDetails;
    }

    /**
     * @throws Exception when the death date (if any) precedes the birthdate
     * @throws Exception when birthdate and death date do not match age (if any)
     */
    public function setDeceasedDetails(?DeceasedDetails $deceasedDetails): self
    {
        $this->assertValidDeceasedDetails($this->bornAt(), $deceasedDetails);
        $this->deceasedDetails = $deceasedDetails;
        $this->clearAgeIfItIsRedundant();

        return $this;
    }

    /**
     * @throws Exception when the birthdate follows the death date (if any)
     */
    private function assertBornAtNotFollowsDiedAt(
        ?\DateTimeImmutable $bornAt,
        ?DeceasedDetails    $deceasedDetails,
    ): void {
        if (!$bornAt || !$deceasedDetails?->diedAt()) {
            return;
        }
        if ($bornAt > $deceasedDetails->diedAt()) {
            throw new Exception('Дата рождения не может следовать за датой смерти.');
        }
    }

    /**
     * @throws Exception when the death date (if any) precedes the birthdate
     * @throws Exception when birthdate and death date do not match age (if any)
     */
    private function assertValidDeceasedDetails(
        ?\DateTimeImmutable $bornAt,
        ?DeceasedDetails    $deceasedDetails
    ): void {
        $this->assertDiedAtNotPrecedesBornAt($bornAt, $deceasedDetails);
        $this->assertBornAtAndDiedAtAreMatchingAge($bornAt, $deceasedDetails);
    }

    /**
     * @throws Exception when the death date (if any) precedes the birthdate
     */
    private function assertDiedAtNotPrecedesBornAt(
        ?\DateTimeImmutable $bornAt,
        ?DeceasedDetails    $deceasedDetails,
    ): void {
        if (!$deceasedDetails?->diedAt() || !$bornAt) {
            return;
        }
        if ($deceasedDetails->diedAt() < $bornAt) {
            throw new Exception('Дата смерти не может предшествовать дате рождения.');
        }
    }

    /**
     * @throws Exception when birthdate and death date do not match age (if any)
     */
    private function assertBornAtAndDiedAtAreMatchingAge(
        ?\DateTimeImmutable $bornAt,
        ?DeceasedDetails    $deceasedDetails,
    ): void {
        if ($deceasedDetails?->age() === null || !$bornAt || !$deceasedDetails?->diedAt()) {
            return;
        }
        if ($deceasedDetails->age()->value() !== $bornAt->diff($deceasedDetails->diedAt())->y) {
            throw new Exception('Даты рождения и смерти не соответствуют возрасту.');
        }
    }

    private function clearAgeIfItIsRedundant(): void
    {
        if ($this->bornAt() !== null && $this->deceasedDetails()?->diedAt() !== null) {
            $this->deceasedDetails = new DeceasedDetails(
                $this->deceasedDetails()?->diedAt(),
                null,
                $this->deceasedDetails()?->causeOfDeathId(),
                $this->deceasedDetails()?->deathCertificate(),
                $this->deceasedDetails()?->cremationCertificate(),
            );
        }
    }
}
