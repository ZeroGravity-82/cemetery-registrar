<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\EntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NaturalPersonFactory extends EntityFactory
{
    /**
     * @param string|null $fullName
     * @param string|null $phone
     * @param string|null $phoneAdditional
     * @param string|null $email
     * @param string|null $address
     * @param string|null $bornAt
     * @param string|null $placeOfBirth
     * @param string|null $passportSeries
     * @param string|null $passportNumber
     * @param string|null $passportIssuedAt
     * @param string|null $passportIssuedBy
     * @param string|null $passportDivisionCode
     *
     * @return NaturalPerson
     */
    public function createForBurialCustomer(
        ?string $fullName,
        ?string $phone,
        ?string $phoneAdditional,
        ?string $email,
        ?string $address,
        ?string $bornAt,
        ?string $placeOfBirth,
        ?string $passportSeries,
        ?string $passportNumber,
        ?string $passportIssuedAt,
        ?string $passportIssuedBy,
        ?string $passportDivisionCode,
    ): NaturalPerson {
        $this->assertFullNameIsProvided($fullName);
        $fullName        = new FullName($fullName);
        $phone           = $phone !== null           ? new PhoneNumber($phone)                                : null;
        $phoneAdditional = $phoneAdditional !== null ? new PhoneNumber($phoneAdditional)                      : null;
        $email           = $email !== null           ? new Email($email)                                      : null;
        $address         = $address !== null         ? new Address($address)                                  : null;
        $bornAt          = $bornAt !== null          ? \DateTimeImmutable::createFromFormat('Y-m-d', $bornAt) : null;
        $placeOfBirth    = $placeOfBirth !== null    ? new PlaceOfBirth($placeOfBirth)                        : null;
        $passport        = $passportSeries !== null && $passportNumber !== null && $passportIssuedAt !== null && $passportIssuedBy !== null
            ? new Passport(
                $passportSeries,
                $passportNumber,
                \DateTimeImmutable::createFromFormat('Y-m-d', $passportIssuedAt),
                $passportIssuedBy,
                $passportDivisionCode
            )
            : null;

        return (new NaturalPerson(
                new NaturalPersonId($this->identityGenerator->getNextIdentity()),
                $fullName,
            ))
            ->setPhone($phone)
            ->setPhoneAdditional($phoneAdditional)
            ->setEmail($email)
            ->setAddress($address)
            ->setBornAt($bornAt)
            ->setPlaceOfBirth($placeOfBirth)
            ->setPassport($passport);
    }

    /**
     * @param string|null $fullName
     * @param string|null $bornAt
     *
     * @return NaturalPerson
     */
    public function createForDeceased(
        ?string $fullName,
        ?string $bornAt,
    ): NaturalPerson {
        $this->assertFullNameIsProvided($fullName);
        $fullName = new FullName($fullName);
        $bornAt   = $bornAt !== null ? \DateTimeImmutable::createFromFormat('Y-m-d', $bornAt) : null;

        return (new NaturalPerson(
                new NaturalPersonId($this->identityGenerator->getNextIdentity()),
                $fullName,
            ))
            ->setBornAt($bornAt);
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
