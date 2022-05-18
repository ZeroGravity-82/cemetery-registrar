<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialFormView
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $code,
        public readonly string  $type,
        public readonly string  $deceasedId,
        public readonly string  $deceasedNaturalPersonId,
        public readonly string  $deceasedNaturalPersonFullName,
        public readonly ?string $deceasedNaturalPersonBornAt,
        public readonly string  $deceasedDiedAt,
        public readonly ?string $deceasedDeathCertificateId,
        public readonly ?string $deceasedCauseOfDeath,
        public readonly ?string $customerId,
        public readonly ?string $customerType,
        public readonly ?string $customerNaturalPersonFullName,
        public readonly ?string $customerNaturalPersonPhone,
        public readonly ?string $customerNaturalPersonPhoneAdditional,
        public readonly ?string $customerNaturalPersonEmail,
        public readonly ?string $customerNaturalPersonAddress,
        public readonly ?string $customerNaturalPersonBornAt,
        public readonly ?string $customerNaturalPersonPlaceOfBirth,
        public readonly ?string $customerNaturalPersonPassport,
        public readonly ?string $customerSoleProprietorName,
        public readonly ?string $customerSoleProprietorInn,
        public readonly ?string $customerSoleProprietorOgrnip,
        public readonly ?string $customerSoleProprietorOkpo,
        public readonly ?string $customerSoleProprietorOkved,
        public readonly ?string $customerSoleProprietorRegistrationAddress,
        public readonly ?string $customerSoleProprietorActualLocationAddress,
        public readonly ?string $customerSoleProprietorBankDetails,
        public readonly ?string $customerSoleProprietorPhone,
        public readonly ?string $customerSoleProprietorPhoneAdditional,
        public readonly ?string $customerSoleProprietorFax,
        public readonly ?string $customerSoleProprietorEmail,
        public readonly ?string $customerSoleProprietorWebsite,
        public readonly ?string $customerJuristicPersonName,
        public readonly ?string $customerJuristicPersonInn,
        public readonly ?string $customerJuristicPersonKpp,
        public readonly ?string $customerJuristicPersonOgrn,
        public readonly ?string $customerJuristicPersonOkpo,
        public readonly ?string $customerJuristicPersonOkved,
        public readonly ?string $customerJuristicPersonLegalAddress,
        public readonly ?string $customerJuristicPersonPostalAddress,
        public readonly ?string $customerJuristicPersonBankDetails,
        public readonly ?string $customerJuristicPersonPhone,
        public readonly ?string $customerJuristicPersonPhoneAdditional,
        public readonly ?string $customerJuristicPersonFax,
        public readonly ?string $customerJuristicPersonGeneralDirector,
        public readonly ?string $customerJuristicPersonEmail,
        public readonly ?string $customerJuristicPersonWebsite,
        public readonly ?string $burialChainId,
        public readonly ?string $burialPlaceId,
        public readonly ?string $burialPlaceType,
        public readonly ?string $burialPlaceGeoPosition,
        public readonly ?string $burialPlaceGraveSiteCemeteryBlockId,
        public readonly ?int    $burialPlaceGraveSiteRowInBlock,
        public readonly ?int    $burialPlaceGraveSitePositionInRow,
        public readonly ?string $burialPlaceGraveSiteSize,
        public readonly ?string $burialPlaceColumbariumNicheColumbariumId,
        public readonly ?int    $burialPlaceColumbariumNicheRowNumber,
        public readonly ?string $burialPlaceColumbariumNicheNicheNumber,
        public readonly ?string $burialPlaceMemorialTreeNumber,
        public readonly ?string $burialPlaceGeoPositionLatitude,
        public readonly ?string $burialPlaceGeoPositionLongitude,
        public readonly ?string $burialPlaceGeoPositionError,



        public readonly ?string $burialPlaceId,
        public readonly ?string $burialPlaceType,
        public readonly ?string $burialPlaceOwnerId,
        public readonly ?string $funeralCompanyId,
        public readonly ?string $funeralCompanyType,
        public readonly ?string $burialContainer,
        public readonly ?string $buriedAt,
    ) {}
}
