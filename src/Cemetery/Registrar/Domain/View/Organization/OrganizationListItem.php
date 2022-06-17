<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationListItem
{
    /**
     * @param string      $id
     * @param string      $typeShortcut
     * @param string      $typeLabel
     * @param string      $name
     * @param string|null $innKpp
     * @param string|null $ogrn
     * @param string|null $okpo
     * @param string|null $okved
     * @param string|null $address
     * @param string|null $bankDetails
     * @param string|null $phone
     * @param string|null $generalDirector
     * @param string|null $emailWebsite
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $typeShortcut,
        public readonly string  $typeLabel,
        public readonly string  $name,
        public readonly ?string $innKpp,
        public readonly ?string $ogrn,
        public readonly ?string $okpo,
        public readonly ?string $okved,
        public readonly ?string $address,
        public readonly ?string $bankDetails,
        public readonly ?string $phone,
        public readonly ?string $generalDirector,
        public readonly ?string $emailWebsite,
    ) {}
}
