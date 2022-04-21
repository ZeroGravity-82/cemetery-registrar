<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\IdentityGenerator;

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
     * @param IdentityGenerator $identityGenerator
     */
    public function __construct(
        private readonly IdentityGenerator $identityGenerator,
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
     * @param string $phone
     *
     * @return $this
     */
    public function addPhone(string $phone): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setPhone($phone);

        return $this;
    }

    /**
     * @param string $phoneAdditional
     *
     * @return $this
     */
    public function addPhoneAdditional(string $phoneAdditional): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setPhoneAdditional($phoneAdditional);

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function addEmail(string $email): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setEmail($email);

        return $this;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function addAddress(string $address): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setAddress($address);

        return $this;
    }

    /**
     * @param \DateTimeImmutable $bornAt
     *
     * @return $this
     */
    public function addBornAt(\DateTimeImmutable $bornAt): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setBornAt($bornAt);

        return $this;
    }

    /**
     * @param string $placeOfBirth
     *
     * @return $this
     */
    public function addPlaceOfBirth(string $placeOfBirth): self
    {
        $this->assertInitialized();
        $this->naturalPerson->setPlaceOfBirth($placeOfBirth);

        return $this;
    }

    /**
     * @param string             $passportSeries
     * @param string             $passportNumber
     * @param \DateTimeImmutable $passportIssuedAt
     * @param string             $passportIssuedBy
     * @param string|null        $passportDivisionCode
     *
     * @return $this
     */
    public function addPassport(
        string             $passportSeries,
        string             $passportNumber,
        \DateTimeImmutable $passportIssuedAt,
        string             $passportIssuedBy,
        ?string            $passportDivisionCode,
    ): self  {
        $this->assertInitialized();
        $passport = new Passport(
            $passportSeries,
            $passportNumber,
            $passportIssuedAt,
            $passportIssuedBy,
            $passportDivisionCode
        );
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
     * @throws \LogicException when the natural person builder is not initialized
     */
    private function assertInitialized(): void
    {
        if (!isset($this->naturalPerson)) {
            throw new \LogicException(\sprintf('Строитель для класса %s не инициализирован.', NaturalPerson::class));
        }
    }
}
