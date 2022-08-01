<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\FuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyListItem
{
    public function __construct(
        public string  $id,
        public string  $organizationType,
        public ?string $organizationJuristicPersonName,
        public ?string $organizationJuristicPersonInn,
        public ?string $organizationJuristicPersonLegalAddress,
        public ?string $organizationJuristicPersonPostalAddress,
        public ?string $organizationJuristicPersonPhone,
        public ?string $organizationSoleProprietorName,
        public ?string $organizationSoleProprietorInn,
        public ?string $organizationSoleProprietorRegistrationAddress,
        public ?string $organizationSoleProprietorActualLocationAddress,
        public ?string $organizationSoleProprietorPhone,
        public ?string $note,
    ) {}
}
