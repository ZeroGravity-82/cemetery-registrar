<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonFactory extends EntityFactory
{
    /**
     * @throws Exception when generating an invalid juristic person ID
     * @throws Exception when the name is invalid
     * @throws Exception when the INN (if any) is invalid
     * @throws Exception when the KPP (if any) is invalid
     * @throws Exception when the OGRN (if any) is invalid
     * @throws Exception when the OKPO (if any) is invalid
     * @throws Exception when the OKVED (if any) is invalid
     * @throws Exception when the legal address (if any) is invalid
     * @throws Exception when the postal address (if any) is invalid
     * @throws Exception when the bank details (if any) are invalid
     * @throws Exception when the phone number (if any) is invalid
     * @throws Exception when the phone number additional (if any) is invalid
     * @throws Exception when the fax (if any) is invalid
     * @throws Exception when the general director (if any) is invalid
     * @throws Exception when the email (if any) is invalid
     * @throws Exception when the website (if any) is invalid
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
        $inn           = $inn           !== null ? new Inn($inn)               : null;
        $kpp           = $kpp           !== null ? new Kpp($kpp)               : null;
        $ogrn          = $ogrn          !== null ? new Ogrn($ogrn)             : null;
        $okpo          = $okpo          !== null ? new Okpo($okpo)             : null;
        $okved         = $okved         !== null ? new Okved($okved)           : null;
        $legalAddress  = $legalAddress  !== null ? new Address($legalAddress)  : null;
        $postalAddress = $postalAddress !== null ? new Address($postalAddress) : null;
        $bankDetails   = $bankDetailsBankName       !== null &&
                         $bankDetailsBik            !== null &&
                         $bankDetailsCurrentAccount !== null
            ? new BankDetails(
                $bankDetailsBankName,
                $bankDetailsBik,
                $bankDetailsCorrespondentAccount,
                $bankDetailsCurrentAccount,
            )
            : null;
        $phone           = $phone           !== null ? new PhoneNumber($phone)           : null;
        $phoneAdditional = $phoneAdditional !== null ? new PhoneNumber($phoneAdditional) : null;
        $fax             = $fax             !== null ? new PhoneNumber($fax)             : null;
        $generalDirector = $generalDirector !== null ? new FullName($generalDirector)    : null;
        $email           = $email           !== null ? new Email($email)                 : null;
        $website         = $website         !== null ? new Website($website)             : null;

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
