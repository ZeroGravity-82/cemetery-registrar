<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\EntityFactory;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonFactory extends EntityFactory
{
    /**
     * @param string|null $name
     * @param string|null $inn
     * @param string|null $kpp
     * @param string|null $ogrn
     * @param string|null $okpo
     * @param string|null $okved
     * @param string|null $legalAddress
     * @param string|null $postalAddress
     * @param string|null $bankDetailsBankName
     * @param string|null $bankDetailsBik
     * @param string|null $bankDetailsCorrespondentAccount
     * @param string|null $bankDetailsCurrentAccount
     * @param string|null $phone
     * @param string|null $phoneAdditional
     * @param string|null $fax
     * @param string|null $generalDirector
     * @param string|null $email
     * @param string|null $website
     *
     * @return JuristicPerson
     */
    public function create(
        ?string $name,
        ?string $inn,
        ?string $kpp,
        ?string $ogrn,
        ?string $okpo,
        ?string $okved,
        ?string $legalAddress,
        ?string $postalAddress,
        ?string $bankDetailsBankName,
        ?string $bankDetailsBik,
        ?string $bankDetailsCorrespondentAccount,
        ?string $bankDetailsCurrentAccount,
        ?string $phone,
        ?string $phoneAdditional,
        ?string $fax,
        ?string $generalDirector,
        ?string $email,
        ?string $website,
    ): JuristicPerson {
        $name          = new Name((string) $name);
        $inn           = $inn !== null           ? new Inn($inn)               : null;
        $kpp           = $kpp !== null           ? new Kpp($kpp)               : null;
        $ogrn          = $ogrn !== null          ? new Ogrn($ogrn)             : null;
        $okpo          = $okpo !== null          ? new Okpo($okpo)             : null;
        $okved         = $okved !== null         ? new Okved($okved)           : null;
        $legalAddress  = $legalAddress !== null  ? new Address($legalAddress)  : null;
        $postalAddress = $postalAddress !== null ? new Address($postalAddress) : null;
        $bankDetails   = $bankDetailsBankName !== null && $bankDetailsBik !== null && $bankDetailsCurrentAccount !== null
            ? new BankDetails($bankDetailsBankName, $bankDetailsBik, $bankDetailsCorrespondentAccount, $bankDetailsCurrentAccount)
            : null;
        $phone           = $phone !== null           ? new PhoneNumber($phone)           : null;
        $phoneAdditional = $phoneAdditional !== null ? new PhoneNumber($phoneAdditional) : null;
        $fax             = $fax !== null             ? new PhoneNumber($fax)             : null;
        $generalDirector = $generalDirector !== null ? new FullName($generalDirector)    : null;
        $email           = $email !== null           ? new Email($email)                 : null;
        $website         = $website !== null         ? new Website($website)             : null;

        return (new JuristicPerson(
                new JuristicPersonId($this->identityGenerator->getNextIdentity()),
                $name,
            ))
            ->setInn($inn)
            ->setKpp($kpp)
            ->setOgrn($ogrn)
            ->setOkpo($okpo)
            ->setOkved($okved)
            ->setLegalAddress($legalAddress)
            ->setPostalAddress($postalAddress)
            ->setBankDetails($bankDetails)
            ->setPhone($phone)
            ->setPhoneAdditional($phoneAdditional)
            ->setFax($fax)
            ->setGeneralDirector($generalDirector)
            ->setEmail($email)
            ->setWebsite($website);
    }
}
