<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPersonDirector
{
    /**
     * @param NaturalPersonBuilder $builder
     */
    public function __construct(
        private NaturalPersonBuilder $builder,
    ) {}

    /**
     * @param string|null             $fullName
     * @param string|null             $phone
     * @param string|null             $phoneAdditional
     * @param string|null             $email
     * @param string|null             $address
     * @param \DateTimeImmutable|null $bornAt
     * @param string|null             $placeOfBirth
     * @param string|null             $passportSeries
     * @param string|null             $passportNumber
     * @param \DateTimeImmutable|null $passportIssuedAt
     * @param string|null             $passportIssuedBy
     * @param string|null             $passportDivisionCode
     *
     * @return NaturalPerson
     */
    public function createNaturalPersonForCustomer(
        ?string             $fullName,
        ?string             $phone,
        ?string             $phoneAdditional,
        ?string             $email,
        ?string             $address,
        ?\DateTimeImmutable $bornAt,
        ?string             $placeOfBirth,
        ?string             $passportSeries,
        ?string             $passportNumber,
        ?\DateTimeImmutable $passportIssuedAt,
        ?string             $passportIssuedBy,
        ?string             $passportDivisionCode,
    ): NaturalPerson {
        $this->assertFullNameIsProvided($fullName);
        $this->builder->initialize($fullName);

        if ($phone !== null) {
            $this->builder->addPhone($phone);
        }
        if ($phoneAdditional !== null) {
            $this->builder->addPhoneAdditional($phoneAdditional);
        }
        if ($email !== null) {
            $this->builder->addEmail($email);
        }
        if ($address !== null) {
            $this->builder->addAddress($address);
        }
        if ($bornAt !== null) {
            $this->builder->addBornAt($bornAt);
        }
        if ($placeOfBirth !== null) {
            $this->builder->addPlaceOfBirth($placeOfBirth);
        }
        if (
            $passportSeries !== null &&
            $passportNumber !== null &&
            $passportIssuedAt !== null &&
            $passportIssuedBy !== null
        ) {
            $this->builder->addPassport(
                $passportSeries,
                $passportNumber,
                $passportIssuedAt,
                $passportIssuedBy,
                $passportDivisionCode,
            );
        }

        return $this->builder->build();
    }

    /**
     * @param string|null             $fullName
     * @param \DateTimeImmutable|null $bornAt
     *
     * @return NaturalPerson
     */
    public function createNaturalPersonForDeceased(
        ?string             $fullName,
        ?\DateTimeImmutable $bornAt,
    ): NaturalPerson {
        $this->assertFullNameIsProvided($fullName);
        $this->builder->initialize($fullName);

        if ($bornAt !== null) {
            $this->builder->addBornAt($bornAt);
        }

        return $this->builder->build();
    }

    /**
     * @param string|null $fullName
     *
     * @throws \RuntimeException when the full name is not provided
     */
    private function assertFullNameIsProvided(?string $fullName): void
    {
        if ($fullName === null) {
            throw new \RuntimeException('ФИО не указано.');
        }
    }
}
