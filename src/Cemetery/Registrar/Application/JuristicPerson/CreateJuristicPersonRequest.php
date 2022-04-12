<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateJuristicPersonRequest
{
    /**
     * @param string      $name
     * @param string|null $inn
     * @param string|null $kpp
     * @param string|null $ogrn
     * @param string|null $okpo
     * @param string|null $okved
     * @param string|null $legalAddress
     * @param string|null $postalAddress
     * @param string|null $bankName
     * @param string|null $bik
     * @param string|null $correspondentAccount
     * @param string|null $currentAccount
     * @param string|null $phone
     * @param string|null $phoneAdditional
     * @param string|null $fax
     * @param string|null $generalDirector
     * @param string|null $email
     * @param string|null $website
     */
    public function __construct(
        public string  $name,
        public ?string $inn,
        public ?string $kpp,
        public ?string $ogrn,
        public ?string $okpo,
        public ?string $okved,
        public ?string $legalAddress,
        public ?string $postalAddress,
        public ?string $bankName,
        public ?string $bik,
        public ?string $correspondentAccount,
        public ?string $currentAccount,
        public ?string $phone,
        public ?string $phoneAdditional,
        public ?string $fax,
        public ?string $generalDirector,
        public ?string $email,
        public ?string $website,
    ) {}
}