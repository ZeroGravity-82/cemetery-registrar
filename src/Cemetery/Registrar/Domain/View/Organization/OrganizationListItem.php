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
        public ?string $innKpp,
        public ?string $ogrn,
        public ?string $okpo,
        public ?string $okved,
        public ?string $address,
        public ?string $bankDetails,
        public ?string $phone,
        public ?string $generalDirector,
        public ?string $emailWebsite,
    ) {}
}
