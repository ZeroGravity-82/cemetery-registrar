<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurialRequest
{
    /**
     * @param string|null $burialType
     * @param string|null $deceasedNaturalPersonId
     * @param string|null $deceasedNaturalPersonFullName
     * @param string|null $deceasedNaturalPersonBornAt
     * @param string|null $deceasedDiedAt
     * @param string|null $deceasedDeathCertificateId
     * @param string|null $deceasedCauseOfDeath
     * @param string|null $customerId
     * @param string|null $customerType
     * @param string|null $customerNaturalPersonFullName
     * @param string|null $customerNaturalPersonPhone
     * @param string|null $customerNaturalPersonPhoneAdditional
     * @param string|null $customerNaturalPersonEmail
     * @param string|null $customerNaturalPersonAddress
     * @param string|null $customerNaturalPersonBornAt
     * @param string|null $customerNaturalPersonPlaceOfBirth
     * @param string|null $customerNaturalPersonPassportSeries
     * @param string|null $customerNaturalPersonPassportNumber
     * @param string|null $customerNaturalPersonPassportIssuedAt
     * @param string|null $customerNaturalPersonPassportIssuedBy
     * @param string|null $customerNaturalPersonPassportDivisionCode
     * @param string|null $customerSoleProprietorName
     * @param string|null $customerSoleProprietorInn
     * @param string|null $customerSoleProprietorOgrnip
     * @param string|null $customerSoleProprietorOkpo
     * @param string|null $customerSoleProprietorOkved
     * @param string|null $customerSoleProprietorRegistrationAddress
     * @param string|null $customerSoleProprietorActualLocationAddress
     * @param string|null $customerSoleProprietorBankName
     * @param string|null $customerSoleProprietorBik
     * @param string|null $customerSoleProprietorCorrespondentAccount
     * @param string|null $customerSoleProprietorCurrentAccount
     * @param string|null $customerSoleProprietorPhone
     * @param string|null $customerSoleProprietorPhoneAdditional
     * @param string|null $customerSoleProprietorFax
     * @param string|null $customerSoleProprietorEmail
     * @param string|null $customerSoleProprietorWebsite
     * @param string|null $customerJuristicPersonName
     * @param string|null $customerJuristicPersonInn
     * @param string|null $customerJuristicPersonKpp
     * @param string|null $customerJuristicPersonOgrn
     * @param string|null $customerJuristicPersonOkpo
     * @param string|null $customerJuristicPersonOkved
     * @param string|null $customerJuristicPersonLegalAddress
     * @param string|null $customerJuristicPersonPostalAddress
     * @param string|null $customerJuristicPersonBankName
     * @param string|null $customerJuristicPersonBik
     * @param string|null $customerJuristicPersonCorrespondentAccount
     * @param string|null $customerJuristicPersonCurrentAccount
     * @param string|null $customerJuristicPersonPhone
     * @param string|null $customerJuristicPersonPhoneAdditional
     * @param string|null $customerJuristicPersonFax
     * @param string|null $customerJuristicPersonGeneralDirector
     * @param string|null $customerJuristicPersonEmail
     * @param string|null $customerJuristicPersonWebsite
     * @param string|null $burialPlaceOwnerId
     * @param string|null $burialPlaceOwnerFullName
     * @param string|null $burialPlaceOwnerPhone
     * @param string|null $burialPlaceOwnerPhoneAdditional
     * @param string|null $burialPlaceOwnerEmail
     * @param string|null $burialPlaceOwnerAddress
     * @param string|null $burialPlaceOwnerBornAt
     * @param string|null $burialPlaceOwnerPlaceOfBirth
     * @param string|null $burialPlaceOwnerPassportSeries
     * @param string|null $burialPlaceOwnerPassportNumber
     * @param string|null $burialPlaceOwnerPassportIssuedAt
     * @param string|null $burialPlaceOwnerPassportIssuedBy
     * @param string|null $burialPlaceOwnerPassportDivisionCode
     * @param string|null $funeralCompanyId
     * @param string|null $burialChainId
     * @param string|null $burialPlaceId
     * @param string|null $burialPlaceType
     * @param string|null $burialPlaceGeolocationPosition
     * @param string|null $burialPlaceGraveSiteCemeteryBlockId
     * @param int|null    $burialPlaceGraveSiteRowInBlock
     * @param int|null    $burialPlaceGraveSitePositionInRow
     * @param string|null $burialPlaceGraveSiteSize
     * @param string|null $burialPlaceColumbariumNicheColumbariumId
     * @param int|null    $burialPlaceColumbariumNicheRowNumber
     * @param string|null $burialPlaceColumbariumNicheNicheNumber
     * @param string|null $burialPlaceMemorialTreeNumber
     * @param string|null $burialPlaceGeoPositionLatitude
     * @param string|null $burialPlaceGeoPositionLongitude
     * @param string|null $burialPlaceGeoPositionError
     * @param string|null $burialContainerId
     * @param string|null $burialContainerType
     * @param string|null $burialContainerCoffinSize
     * @param string|null $burialContainerCoffinIsStandard
     * @param string|null $burialContainerCoffinType
     * @param string|null $buriedAt
     */
    public function __construct(
        public readonly ?string $burialType,
        public readonly ?string $deceasedNaturalPersonId,
        public readonly ?string $deceasedNaturalPersonFullName,
        public readonly ?string $deceasedNaturalPersonBornAt,
        public readonly ?string $deceasedDiedAt,
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
        public readonly ?string $customerNaturalPersonPassportSeries,
        public readonly ?string $customerNaturalPersonPassportNumber,
        public readonly ?string $customerNaturalPersonPassportIssuedAt,
        public readonly ?string $customerNaturalPersonPassportIssuedBy,
        public readonly ?string $customerNaturalPersonPassportDivisionCode,
        public readonly ?string $customerSoleProprietorName,
        public readonly ?string $customerSoleProprietorInn,
        public readonly ?string $customerSoleProprietorOgrnip,
        public readonly ?string $customerSoleProprietorOkpo,
        public readonly ?string $customerSoleProprietorOkved,
        public readonly ?string $customerSoleProprietorRegistrationAddress,
        public readonly ?string $customerSoleProprietorActualLocationAddress,
        public readonly ?string $customerSoleProprietorBankName,
        public readonly ?string $customerSoleProprietorBik,
        public readonly ?string $customerSoleProprietorCorrespondentAccount,
        public readonly ?string $customerSoleProprietorCurrentAccount,
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
        public readonly ?string $customerJuristicPersonBankName,
        public readonly ?string $customerJuristicPersonBik,
        public readonly ?string $customerJuristicPersonCorrespondentAccount,
        public readonly ?string $customerJuristicPersonCurrentAccount,
        public readonly ?string $customerJuristicPersonPhone,
        public readonly ?string $customerJuristicPersonPhoneAdditional,
        public readonly ?string $customerJuristicPersonFax,
        public readonly ?string $customerJuristicPersonGeneralDirector,
        public readonly ?string $customerJuristicPersonEmail,
        public readonly ?string $customerJuristicPersonWebsite,
        public readonly ?string $burialPlaceOwnerId,
        public readonly ?string $burialPlaceOwnerFullName,
        public readonly ?string $burialPlaceOwnerPhone,
        public readonly ?string $burialPlaceOwnerPhoneAdditional,
        public readonly ?string $burialPlaceOwnerEmail,
        public readonly ?string $burialPlaceOwnerAddress,
        public readonly ?string $burialPlaceOwnerBornAt,
        public readonly ?string $burialPlaceOwnerPlaceOfBirth,
        public readonly ?string $burialPlaceOwnerPassportSeries,
        public readonly ?string $burialPlaceOwnerPassportNumber,
        public readonly ?string $burialPlaceOwnerPassportIssuedAt,
        public readonly ?string $burialPlaceOwnerPassportIssuedBy,
        public readonly ?string $burialPlaceOwnerPassportDivisionCode,
        public readonly ?string $funeralCompanyId,
        public readonly ?string $burialChainId,
        public readonly ?string $burialPlaceId,
        public readonly ?string $burialPlaceType,
        public readonly ?string $burialPlaceGeolocationPosition,
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
        public readonly ?string $burialContainerId,
        public readonly ?string $burialContainerType,
        public readonly ?string $burialContainerCoffinSize,
        public readonly ?string $burialContainerCoffinIsStandard,
        public readonly ?string $burialContainerCoffinType,
        public readonly ?string $buriedAt,
    ) {}
}
