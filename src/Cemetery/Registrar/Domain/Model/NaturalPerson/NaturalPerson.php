<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\AggregateRoot;

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
     */
    public function setBornAt(?\DateTimeImmutable $bornAt): self
    {
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
}
