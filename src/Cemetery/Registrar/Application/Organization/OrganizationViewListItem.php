<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OrganizationViewListItem
{
    /**
     * @param string      $id
     * @param string      $type
     * @param string|null $juristicPersonName
     * @param string|null $juristicPersonInn
     * @param string|null $juristicPersonKpp
     * @param string|null $juristicPersonOgrn
     * @param string|null $juristicPersonOkpo
     * @param string|null $juristicPersonOkved
     * @param string|null $juristicPersonLegalAddress
     * @param string|null $juristicPersonPostalAddress
     * @param string|null $juristicPersonBankDetails
     * @param string|null $juristicPersonPhone
     * @param string|null $juristicPersonPhoneAdditional
     * @param string|null $juristicPersonFax
     * @param string|null $juristicPersonGeneralDirector
     * @param string|null $juristicPersonEmail
     * @param string|null $juristicPersonWebsite
     * @param string|null $soleProprietorName
     * @param string|null $soleProprietorInn
     * @param string|null $soleProprietorOgrnip
     * @param string|null $soleProprietorOkpo
     * @param string|null $soleProprietorOkved
     * @param string|null $soleProprietorRegistrationAddress
     * @param string|null $soleProprietorActualLocationAddress
     * @param string|null $soleProprietorBankDetails
     * @param string|null $soleProprietorPhone
     * @param string|null $soleProprietorPhoneAdditional
     * @param string|null $soleProprietorFax
     * @param string|null $soleProprietorEmail
     * @param string|null $soleProprietorWebsite
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $type,
        public readonly ?string $juristicPersonName,
        public readonly ?string $juristicPersonInn,
        public readonly ?string $juristicPersonKpp,
        public readonly ?string $juristicPersonOgrn,
        public readonly ?string $juristicPersonOkpo,
        public readonly ?string $juristicPersonOkved,
        public readonly ?string $juristicPersonLegalAddress,
        public readonly ?string $juristicPersonPostalAddress,
        public readonly ?string $juristicPersonBankDetails,
        public readonly ?string $juristicPersonPhone,
        public readonly ?string $juristicPersonPhoneAdditional,
        public readonly ?string $juristicPersonFax,
        public readonly ?string $juristicPersonGeneralDirector,
        public readonly ?string $juristicPersonEmail,
        public readonly ?string $juristicPersonWebsite,
        public readonly ?string $soleProprietorName,
        public readonly ?string $soleProprietorInn,
        public readonly ?string $soleProprietorOgrnip,
        public readonly ?string $soleProprietorOkpo,
        public readonly ?string $soleProprietorOkved,
        public readonly ?string $soleProprietorRegistrationAddress,
        public readonly ?string $soleProprietorActualLocationAddress,
        public readonly ?string $soleProprietorBankDetails,
        public readonly ?string $soleProprietorPhone,
        public readonly ?string $soleProprietorPhoneAdditional,
        public readonly ?string $soleProprietorFax,
        public readonly ?string $soleProprietorEmail,
        public readonly ?string $soleProprietorWebsite,
    ) {}
}
