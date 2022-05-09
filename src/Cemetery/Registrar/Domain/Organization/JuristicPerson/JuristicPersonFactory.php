<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Contact\Address;
use Cemetery\Registrar\Domain\Contact\Email;
use Cemetery\Registrar\Domain\Contact\PhoneNumber;
use Cemetery\Registrar\Domain\Contact\Website;
use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\Okved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonFactory
{
    /**
     * @param IdentityGenerator $identityGenerator
     */
    public function __construct(
        private readonly IdentityGenerator $identityGenerator,
    ) {}

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
        $this->assertNameIsProvided($name);
        $name          = new Name($name);
        $inn           = $inn           ? new Inn($inn)               : null;
        $kpp           = $kpp           ? new Kpp($kpp)               : null;
        $ogrn          = $ogrn          ? new Ogrn($ogrn)             : null;
        $okpo          = $okpo          ? new Okpo($okpo)             : null;
        $okved         = $okved         ? new Okved($okved)           : null;
        $legalAddress  = $legalAddress  ? new Address($legalAddress)  : null;
        $postalAddress = $postalAddress ? new Address($postalAddress) : null;
        $bankDetails   = $bankDetailsBankName && $bankDetailsBik && $bankDetailsCurrentAccount
            ? new BankDetails($bankDetailsBankName, $bankDetailsBik, $bankDetailsCorrespondentAccount, $bankDetailsCurrentAccount)
            : null;
        $phone           = $phone           ? new PhoneNumber($phone)           : null;
        $phoneAdditional = $phoneAdditional ? new PhoneNumber($phoneAdditional) : null;
        $fax             = $fax             ? new PhoneNumber($fax)             : null;
        $generalDirector = $generalDirector ? new FullName($generalDirector)    : null;
        $email           = $email           ? new Email($email)                 : null;
        $website         = $website         ? new Website($website)             : null;

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

    /**
     * @param string|null $name
     *
     * @throws \RuntimeException when the name is not provided
     */
    private function assertNameIsProvided(?string $name): void
    {
        if ($name === null) {
            throw new \RuntimeException('Наименование не указано.');
        }
    }
}
