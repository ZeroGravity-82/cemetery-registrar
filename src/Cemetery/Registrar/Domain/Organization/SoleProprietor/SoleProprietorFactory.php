<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class SoleProprietorFactory
{
    /**
     * @param SoleProprietorBuilder $builder
     */
    public function __construct(
        private SoleProprietorBuilder $builder,
    ) {}

    /**
     * @param string|null $name
     * @param string|null $inn
     * @param string|null $ogrnip
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
    public function createSoleProprietorForCustomer(
        ?string             $name,
        ?string             $inn,
        ?string             $ogrnip,
        ?string             $registrationAddress,
        ?string             $actualLocationAddress,
        ?string             $bankDetailsBankName,
        ?string             $bankDetailsBik,
        ?string             $bankDetailsCorrespondentAccount,
        ?string             $bankDetailsCurrentAccount,
        ?string             $phone,
        ?string             $phoneAdditional,
        ?string             $fax,
        ?string             $email,
        ?string             $website,
    ): SoleProprietor {
        $this->assertNameIsProvided($name);
        $this->builder->initialize($name);

        if ($inn !== null) {
            $this->builder->addInn($inn);
        }
        if ($ogrnip !== null) {
            $this->builder->addOgrnip($ogrnip);
        }
        if ($registrationAddress !== null) {
            $this->builder->addRegistrationAddress($registrationAddress);
        }
        if ($actualLocationAddress !== null) {
            $this->builder->addActualLocationAddress($actualLocationAddress);
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
