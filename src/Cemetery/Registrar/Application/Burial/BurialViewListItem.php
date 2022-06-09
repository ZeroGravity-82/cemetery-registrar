<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialViewListItem
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly ?string $deceasedNaturalPersonFullName,
        public readonly ?string $deceasedNaturalPersonBornAt,
        public readonly ?string $deceasedDiedAt,
        public readonly ?int    $deceasedAge,
        public readonly ?string $buriedAt,
        public readonly ?string $burialPlaceType,
        public readonly ?string $burialPlaceGraveSiteCemeteryBlockName,
        public readonly ?int    $burialPlaceGraveSiteRowInBlock,
        public readonly ?int    $burialPlaceGraveSitePositionInRow,
        public readonly ?string $burialPlaceColumbariumNicheColumbariumName,
        public readonly ?int    $burialPlaceColumbariumNicheRowInColumbarium,
        public readonly ?string $burialPlaceColumbariumNicheNumber,
        public readonly ?string $burialPlaceMemorialTreeNumber,
        public readonly ?string $customerType,
        public readonly ?string $customerNaturalPersonFullName,
        public readonly ?string $customerNaturalPersonAddress,
        public readonly ?string $customerNaturalPersonPhone,
        public readonly ?string $customerSoleProprietorName,
        public readonly ?string $customerSoleProprietorRegistrationAddress,
        public readonly ?string $customerSoleProprietorActualLocationAddress,
        public readonly ?string $customerSoleProprietorPhone,
        public readonly ?string $customerJuristicPersonName,
        public readonly ?string $customerJuristicPersonLegalAddress,
        public readonly ?string $customerJuristicPersonPostalAddress,
        public readonly ?string $customerJuristicPersonPhone,
    ) {}
}
