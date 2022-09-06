<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationListItem
{
    public function __construct(
        public string  $id,
        public string  $typeShortcut,
        public string  $typeLabel,
        public string  $name,
        public ?string $inn,
        public ?string $kpp,
        public ?string $ogrn,
        public ?string $okpo,
        public ?string $okved,
        public ?string $address1,
        public ?string $address2,
        public ?string $bankDetailsBankName,
        public ?string $bankDetailsBik,
        public ?string $bankDetailsCorrespondentAccount,
        public ?string $bankDetailsCurrentAccount,
        public ?string $phone,
        public ?string $phoneAdditional,
        public ?string $fax,
        public ?string $generalDirector,
        public ?string $email,
        public ?string $website,
    ) {}
}
