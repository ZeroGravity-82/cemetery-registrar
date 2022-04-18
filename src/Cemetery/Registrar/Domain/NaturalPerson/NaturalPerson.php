<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPerson extends AbstractAggregateRoot
{
    /**
     * @var string|null
     */
    private ?string $phone = null;

    /**
     * @var string|null
     */
    private ?string $phoneAdditional = null;

    /**
     * @var string|null
     */
    private ?string $email = null;

    /**
     * @var string|null
     */
    private ?string $address = null;

    /**
     * @var \DateTimeImmutable|null
     */
    private ?\DateTimeImmutable $bornAt = null;

    /**
     * @var string|null
     */
    private ?string $placeOfBirth = null;

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
     * @return string|null
     */
    public function phone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     *
     * @return $this
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function phoneAdditional(): ?string
    {
        return $this->phoneAdditional;
    }

    /**
     * @param string|null $phoneAdditional
     *
     * @return $this
     */
    public function setPhoneAdditional(?string $phoneAdditional): self
    {
        $this->phoneAdditional = $phoneAdditional;

        return $this;
    }

    /**
     * @return string|null
     */
    public function email(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function address(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     *
     * @return $this
     */
    public function setAddress(?string $address): self
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
     * @return string|null
     */
    public function placeOfBirth(): ?string
    {
        return $this->placeOfBirth;
    }

    /**
     * @param string|null $placeOfBirth
     *
     * @return $this
     */
    public function setPlaceOfBirth(?string $placeOfBirth): self
    {
        $this->placeOfBirth = $placeOfBirth;

        return $this;
    }
}
