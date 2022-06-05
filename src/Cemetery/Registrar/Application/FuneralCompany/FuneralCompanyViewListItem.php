<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyViewListItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $inn,
        public readonly ?string $juristicPersonLegalAddress,
        public readonly ?string $juristicPersonPostalAddress,
        public readonly ?string $juristicPersonPhone,
        public readonly ?string $soleProprietorRegistrationAddress,
        public readonly ?string $soleProprietorActualLocationAddress,
        public readonly ?string $soleProprietorPhone,
    ) {}
}
