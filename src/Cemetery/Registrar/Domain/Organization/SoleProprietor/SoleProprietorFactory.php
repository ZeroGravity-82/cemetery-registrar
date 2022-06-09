<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\EntityFactory;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorFactory extends EntityFactory
{
    /**
     * @param string|null $name
     * @param string|null $inn
     * @param string|null $ogrnip
     * @param string|null $okpo
     * @param string|null $okved
     * @param string|null $registrationAddress
     * @param string|null $actualLocationAddress
     * @param string|null $bankDetailsBankName
     * @param string|null $bankDetailsBik
     * @param string|null $bankDetailsCorrespondentAccount
     * @param string|null $bankDetailsCurrentAccount
     * @param string|null $phone
     * @param string|null $phoneAdditional
     * @param string|null $fax
     * @param string|null $email
     * @param string|null $website
     *
     * @return SoleProprietor
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
        $inn                   = $inn !== null                   ? new Inn($inn)                       : null;
        $ogrnip                = $ogrnip !== null                ? new Ogrnip($ogrnip)                 : null;
        $okpo                  = $okpo !== null                  ? new Okpo($okpo)                     : null;
        $okved                 = $okved !== null                 ? new Okved($okved)                   : null;
        $registrationAddress   = $registrationAddress !== null   ? new Address($registrationAddress)   : null;
        $actualLocationAddress = $actualLocationAddress !== null ? new Address($actualLocationAddress) : null;
        $bankDetails   = $bankDetailsBankName !== null && $bankDetailsBik !== null && $bankDetailsCurrentAccount !== null
            ? new BankDetails($bankDetailsBankName, $bankDetailsBik, $bankDetailsCorrespondentAccount, $bankDetailsCurrentAccount)
            : null;
        $phone           = $phone !== null           ? new PhoneNumber($phone)           : null;
        $phoneAdditional = $phoneAdditional !== null ? new PhoneNumber($phoneAdditional) : null;
        $fax             = $fax !== null             ? new PhoneNumber($fax)             : null;
        $email           = $email !== null           ? new Email($email)                 : null;
        $website         = $website !== null         ? new Website($website)             : null;

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
