<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonFactory
{
    /**
     * @param JuristicPersonBuilder $builder
     */
    public function __construct(
        private JuristicPersonBuilder $builder,
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
        $this->builder->initialize($name);

        if ($inn !== null) {
            $this->builder->addInn($inn);
        }
        if ($kpp !== null) {
            $this->builder->addKpp($kpp);
        }
        if ($ogrn !== null) {
            $this->builder->addOgrn($ogrn);
        }
        if ($okpo !== null) {
            $this->builder->addOkpo($okpo);
        }
        if ($okved !== null) {
            $this->builder->addOkved($okved);
        }
        if ($legalAddress !== null) {
            $this->builder->addLegalAddress($legalAddress);
        }
        if ($postalAddress !== null) {
            $this->builder->addPostalAddress($postalAddress);
        }
        if (
            $bankDetailsBankName !== null &&
            $bankDetailsBik !== null &&
            $bankDetailsCurrentAccount !== null
        ) {
            $this->builder->addBankDetails(
                $bankDetailsBankName,
                $bankDetailsBik,
                $bankDetailsCorrespondentAccount,
                $bankDetailsCurrentAccount,
            );
        }
        if ($phone !== null) {
            $this->builder->addPhone($phone);
        }
        if ($phoneAdditional !== null) {
            $this->builder->addPhoneAdditional($phoneAdditional);
        }
        if ($fax !== null) {
            $this->builder->addFax($fax);
        }
        if ($generalDirector !== null) {
            $this->builder->addGeneralDirector($generalDirector);
        }
        if ($email !== null) {
            $this->builder->addEmail($email);
        }
        if ($website !== null) {
            $this->builder->addWebsite($website);
        }

        return $this->builder->build();
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
