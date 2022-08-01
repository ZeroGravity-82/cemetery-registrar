<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Contact\Address;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\Model\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Model\Contact\Website;
use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorFactory extends EntityFactory
{
    /**
     * @throws Exception when generating an invalid sole proprietor ID
     * @throws Exception when the name is invalid
     * @throws Exception when the INN is invalid (if any)
     * @throws Exception when the OGRNIP is invalid (if any)
     * @throws Exception when the OKPO is invalid (if any)
     * @throws Exception when the OKVED is invalid (if any)
     * @throws Exception when the registration address is invalid (if any)
     * @throws Exception when the actual location address is invalid (if any)
     * @throws Exception when the bank details are invalid (if any)
     * @throws Exception when the phone number is invalid (if any)
     * @throws Exception when the phone number additional is invalid (if any)
     * @throws Exception when the fax is invalid (if any)
     * @throws Exception when the email is invalid (if any)
     * @throws Exception when the website is invalid (if any)
     */
    public function create(
        ?string $name,
        ?string $inn,
        ?string $ogrnip,
        ?string $okpo,
        ?string $okved,
        ?string $registrationAddress,
        ?string $actualLocationAddress,
        ?string $bankDetailsBankName,
        ?string $bankDetailsBik,
        ?string $bankDetailsCorrespondentAccount,
        ?string $bankDetailsCurrentAccount,
        ?string $phone,
        ?string $phoneAdditional,
        ?string $fax,
        ?string $email,
        ?string $website,
    ): SoleProprietor {
        $name                  = new Name((string) $name);
        $inn                   = $inn                   !== null ? new Inn($inn)                       : null;
        $ogrnip                = $ogrnip                !== null ? new Ogrnip($ogrnip)                 : null;
        $okpo                  = $okpo                  !== null ? new Okpo($okpo)                     : null;
        $okved                 = $okved                 !== null ? new Okved($okved)                   : null;
        $registrationAddress   = $registrationAddress   !== null ? new Address($registrationAddress)   : null;
        $actualLocationAddress = $actualLocationAddress !== null ? new Address($actualLocationAddress) : null;
        $bankDetails           = $bankDetailsBankName       !== null &&
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
        $email           = $email           !== null ? new Email($email)                 : null;
        $website         = $website         !== null ? new Website($website)             : null;

        return (new SoleProprietor(
            new SoleProprietorId($this->identityGenerator->getNextIdentity()),
            $name,
        ))
            ->setInn($inn)
            ->setOgrnip($ogrnip)
            ->setOkpo($okpo)
            ->setOkved($okved)
            ->setRegistrationAddress($registrationAddress)
            ->setActualLocationAddress($actualLocationAddress)
            ->setBankDetails($bankDetails)
            ->setPhone($phone)
            ->setPhoneAdditional($phoneAdditional)
            ->setFax($fax)
            ->setEmail($email)
            ->setWebsite($website);
    }
}
