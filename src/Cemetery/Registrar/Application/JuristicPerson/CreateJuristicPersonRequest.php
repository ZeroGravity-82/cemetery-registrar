<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateJuristicPersonRequest
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $inn,
        public readonly ?string $kpp,
        public readonly ?string $ogrn,
        public readonly ?string $okpo,
        public readonly ?string $okved,
        public readonly ?string $legalAddress,
        public readonly ?string $postalAddress,
        public readonly ?string $bankDetailsBankName,
        public readonly ?string $bankDetailsBik,
        public readonly ?string $bankDetailsCorrespondentAccount,
        public readonly ?string $bankDetailsCurrentAccount,
        public readonly ?string $phone,
        public readonly ?string $phoneAdditional,
        public readonly ?string $fax,
        public readonly ?string $generalDirector,
        public readonly ?string $email,
        public readonly ?string $website,
    ) {}
}
