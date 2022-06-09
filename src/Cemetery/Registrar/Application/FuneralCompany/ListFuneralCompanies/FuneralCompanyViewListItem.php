<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\ListFuneralCompanies;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyViewListItem
{
    /**
     * @param string      $id
     * @param string      $organizationType
     * @param string|null $organizationJuristicPersonName
     * @param string|null $organizationJuristicPersonInn
     * @param string|null $organizationJuristicPersonLegalAddress
     * @param string|null $organizationJuristicPersonPostalAddress
     * @param string|null $organizationJuristicPersonPhone
     * @param string|null $organizationSoleProprietorName
     * @param string|null $organizationSoleProprietorInn
     * @param string|null $organizationSoleProprietorRegistrationAddress
     * @param string|null $organizationSoleProprietorActualLocationAddress
     * @param string|null $organizationSoleProprietorPhone
     * @param string|null $note
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $organizationType,
        public readonly ?string $organizationJuristicPersonName,
        public readonly ?string $organizationJuristicPersonInn,
        public readonly ?string $organizationJuristicPersonLegalAddress,
        public readonly ?string $organizationJuristicPersonPostalAddress,
        public readonly ?string $organizationJuristicPersonPhone,
        public readonly ?string $organizationSoleProprietorName,
        public readonly ?string $organizationSoleProprietorInn,
        public readonly ?string $organizationSoleProprietorRegistrationAddress,
        public readonly ?string $organizationSoleProprietorActualLocationAddress,
        public readonly ?string $organizationSoleProprietorPhone,
        public readonly ?string $note,
    ) {}
}
