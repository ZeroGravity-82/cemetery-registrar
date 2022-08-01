<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialListItem
{
    public function __construct(
        public string  $id,
        public string  $code,
        public string  $deceasedNaturalPersonFullName,
        public ?string $deceasedNaturalPersonBornAt,
        public string  $deceasedNaturalPersonDeceasedDetailsDiedAt,
        public ?int    $deceasedNaturalPersonDeceasedDetailsAge,
        public ?string $buriedAt,
        public ?string $burialPlaceType,
        public ?string $burialPlaceGraveSiteCemeteryBlockName,
        public ?int    $burialPlaceGraveSiteRowInBlock,
        public ?int    $burialPlaceGraveSitePositionInRow,
        public ?string $burialPlaceColumbariumNicheColumbariumName,
        public ?int    $burialPlaceColumbariumNicheRowInColumbarium,
        public ?string $burialPlaceColumbariumNicheNumber,
        public ?string $burialPlaceMemorialTreeNumber,
        public ?string $customerType,
        public ?string $customerNaturalPersonFullName,
        public ?string $customerNaturalPersonAddress,
        public ?string $customerNaturalPersonPhone,
        public ?string $customerSoleProprietorName,
        public ?string $customerSoleProprietorRegistrationAddress,
        public ?string $customerSoleProprietorActualLocationAddress,
        public ?string $customerSoleProprietorPhone,
        public ?string $customerJuristicPersonName,
        public ?string $customerJuristicPersonLegalAddress,
        public ?string $customerJuristicPersonPostalAddress,
        public ?string $customerJuristicPersonPhone,
    ) {}
}
