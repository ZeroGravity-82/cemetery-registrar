<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonFactory extends EntityFactory
{
    /**
     * @throws Exception       when generating an invalid natural person ID
     * @throws Exception       when the full name is invalid
     * @throws Exception       when the phone number is invalid (if any)
     * @throws Exception       when the phone number additional is invalid (if any)
     * @throws Exception       when the e-mail address is invalid (if any)
     * @throws Exception       when the address is invalid (if any)
     * @throws \LogicException when the birthdate has invalid format (if any)
     * @throws Exception       when the place of birth is invalid (if any)
     * @throws Exception       when the passport series is invalid (if any)
     * @throws Exception       when the passport number is invalid (if any)
     * @throws \LogicException when the passport issue date has invalid format
     * @throws Exception       when the passport issuing authority name is invalid (if any)
     * @throws Exception       when the passport division code is invalid (if any)
     */
    public function create(
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
        $fullName        = new FullName((string) $fullName);
        $phone           = $phone           !== null ? new PhoneNumber($phone)                                : null;
        $phoneAdditional = $phoneAdditional !== null ? new PhoneNumber($phoneAdditional)                      : null;
        $email           = $email           !== null ? new Email($email)                                      : null;
        $address         = $address         !== null ? new Address($address)                                  : null;
        $bornAt          = $bornAt          !== null ? \DateTimeImmutable::createFromFormat('Y-m-d', $bornAt) : null;
        if ($bornAt === false) {
            $this->throwInvalidDateFormatException('рождения');
        }
        $placeOfBirth = $placeOfBirth !== null ? new PlaceOfBirth($placeOfBirth) : null;
        $passport     = null;
        if ($passportSeries   !== null &&
            $passportNumber   !== null &&
            $passportIssuedAt !== null &&
            $passportIssuedBy !== null
        ) {
            $passportIssuedAt = \DateTimeImmutable::createFromFormat('Y-m-d', $passportIssuedAt);
            if ($passportIssuedAt === false) {
                $this->throwInvalidDateFormatException('выдачи паспорта');
            }
            $passport = new Passport(
                $passportSeries,
                $passportNumber,
                $passportIssuedAt,
                $passportIssuedBy,
                $passportDivisionCode
            );
        }

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
     * @throws \LogicException about invalid date format
     */
    private function throwInvalidDateFormatException(string $name): void
    {
        throw new \LogicException(\sprintf('Неверный формат даты %s.', $name));
    }
}
