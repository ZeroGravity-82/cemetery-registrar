<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\IdentityGeneratorInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPersonBuilder
{
    /**
     * @var NaturalPerson
     */
    private NaturalPerson $naturalPerson;

    /**
     * @param IdentityGeneratorInterface $identityGenerator
     */
    public function __construct(
        private IdentityGeneratorInterface $identityGenerator,
    ) {}

    /**
     * @param string $fullName
     *
     * @return $this
     */
    public function initialize(string $fullName): self
    {
        $fullName            = new FullName($fullName);
        $this->naturalPerson = new NaturalPerson(
            new NaturalPersonId($this->identityGenerator->getNextIdentity()),
            $fullName,
        );

        return $this;
    }

    /**
     * @param string|null $phone
     *
     * @return $this
     */
    public function addPhone(?string $phone): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setPhone($phone);

        return $this;
    }

    /**
     * @param string|null $phoneAdditional
     *
     * @return $this
     */
    public function addPhoneAdditional(?string $phoneAdditional): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setPhoneAdditional($phoneAdditional);

        return $this;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     */
    public function addEmail(?string $email): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setEmail($email);

        return $this;
    }

    /**
     * @param string|null $address
     *
     * @return $this
     */
    public function addAddress(?string $address): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setAddress($address);

        return $this;
    }

    /**
     * @param \DateTimeImmutable|null $bornAt
     *
     * @return $this
     */
    public function addBornAt(?\DateTimeImmutable $bornAt): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setBornAt($bornAt);

        return $this;
    }

    /**
     * @param string|null $placeOfBirth
     *
     * @return $this
     */
    public function addPlaceOfBirth(?string $placeOfBirth): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setPlaceOfBirth($placeOfBirth);

        return $this;
    }

    /**
     * @param string|null             $passportSeries
     * @param string|null             $passportNumber
     * @param \DateTimeImmutable|null $passportIssuedAt
     * @param string|null             $passportIssuedBy
     * @param string|null             $passportDivisionCode
     *
     * @return $this
     */
    public function addPassport(
        ?string             $passportSeries,
        ?string             $passportNumber,
        ?\DateTimeImmutable $passportIssuedAt,
        ?string             $passportIssuedBy,
        ?string             $passportDivisionCode,
    ): self
    {
        $this->assertInitialized();
        if (
            $passportSeries       === null &&
            $passportNumber       === null &&
            $passportIssuedAt     === null &&
            $passportIssuedBy     === null &&
            $passportDivisionCode === null
        ) {
            $passport = null;
        } else {
            $passport = new Passport(
                $passportSeries,
                $passportNumber,
                $passportIssuedAt,
                $passportIssuedBy,
                $passportDivisionCode
            );
        }
        $this->naturalPerson->setPassport($passport);

        return $this;
    }

    /**
     * @return NaturalPerson
     */
    public function build(): NaturalPerson
    {
        $this->assertInitialized();
        $naturalPerson = $this->naturalPerson;
        unset($this->naturalPerson);

        return $naturalPerson;
    }

    /**
     * @throws \LogicException when the natural person is not initialized
     */
    private function assertInitialized(): void
    {
        if (!isset($this->naturalPerson)) {
            throw new \LogicException('The natural person is not initialized.');
        }
    }
}
