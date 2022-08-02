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
     * @throws Exception       when the phone number (if any) is invalid
     * @throws Exception       when the phone number additional (if any) is invalid
     * @throws Exception       when the e-mail address (if any) is invalid
     * @throws Exception       when the address (if any) is invalid
     * @throws \LogicException when the birthdate (if any) has invalid format
     * @throws Exception       when the place of birth (if any) is invalid
     * @throws Exception       when the passport series (if any) is invalid
     * @throws Exception       when the passport number (if any) is invalid
     * @throws \LogicException when the passport issue date has invalid format
     * @throws Exception       when the passport issuing authority name (if any) is invalid
     * @throws Exception       when the passport division code (if any) is invalid
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
            $this->throwInvalidDateFormatException('даты рождения');
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
                $this->throwInvalidDateFormatException('даты выдачи паспорта');
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
        throw new \LogicException(\sprintf('Неверный формат %s.', $name));
    }
}
