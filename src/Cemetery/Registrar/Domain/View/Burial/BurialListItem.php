<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialListItem
{
    /**
     * @param string      $id
     * @param string      $code
     * @param string      $deceasedNaturalPersonFullName
     * @param string|null $deceasedNaturalPersonBornAt
     * @param string      $deceasedDiedAt
     * @param int|null    $deceasedAge
     * @param string|null $buriedAt
     * @param string|null $burialPlaceType
     * @param string|null $burialPlaceGraveSiteCemeteryBlockName
     * @param int|null    $burialPlaceGraveSiteRowInBlock
     * @param int|null    $burialPlaceGraveSitePositionInRow
     * @param string|null $burialPlaceColumbariumNicheColumbariumName
     * @param int|null    $burialPlaceColumbariumNicheRowInColumbarium
     * @param string|null $burialPlaceColumbariumNicheNumber
     * @param string|null $burialPlaceMemorialTreeNumber
     * @param string|null $customerType
     * @param string|null $customerNaturalPersonFullName
     * @param string|null $customerNaturalPersonAddress
     * @param string|null $customerNaturalPersonPhone
     * @param string|null $customerSoleProprietorName
     * @param string|null $customerSoleProprietorRegistrationAddress
     * @param string|null $customerSoleProprietorActualLocationAddress
     * @param string|null $customerSoleProprietorPhone
     * @param string|null $customerJuristicPersonName
     * @param string|null $customerJuristicPersonLegalAddress
     * @param string|null $customerJuristicPersonPostalAddress
     * @param string|null $customerJuristicPersonPhone
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly string  $deceasedNaturalPersonFullName,
        public readonly ?string $deceasedNaturalPersonBornAt,
        public readonly string  $deceasedDiedAt,
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
