<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurialRequest
{
    /**
     * @param string|null             $deceasedNaturalPersonId
     * @param string|null             $deceasedNaturalPersonFullName
     * @param \DateTimeImmutable|null $deceasedNaturalPersonBornAt
     * @param \DateTimeImmutable      $deceasedDiedAt
     * @param string|null             $deceasedDeathCertificateId
     * @param string|null             $deceasedCauseOfDeath
     * @param string|null             $customerId
     * @param string|null             $customerType
     * @param string|null             $customerNaturalPersonFullName
     * @param string|null             $customerNaturalPersonPhone
     * @param string|null             $customerNaturalPersonPhoneAdditional
     * @param string|null             $customerNaturalPersonEmail
     * @param string|null             $customerNaturalPersonAddress
     * @param \DateTimeImmutable|null $customerNaturalPersonBornAt
     * @param string|null             $customerNaturalPersonPlaceOfBirth
     * @param string|null             $customerNaturalPersonPassportSeries
     * @param string|null             $customerNaturalPersonPassportNumber
     * @param \DateTimeImmutable|null $customerNaturalPersonPassportIssuedAt
     * @param string|null             $customerNaturalPersonPassportIssuedBy
     * @param string|null             $customerNaturalPersonPassportDivisionCode
     * @param string|null             $customerSoleProprietorName
     * @param string|null             $customerSoleProprietorInn
     * @param string|null             $customerSoleProprietorOgrnip
     * @param string|null             $customerSoleProprietorOkpo
     * @param string|null             $customerSoleProprietorOkved
     * @param string|null             $customerSoleProprietorRegistrationAddress
     * @param string|null             $customerSoleProprietorActualLocationAddress
     * @param string|null             $customerSoleProprietorBankName
     * @param string|null             $customerSoleProprietorBik
     * @param string|null             $customerSoleProprietorCorrespondentAccount
     * @param string|null             $customerSoleProprietorCurrentAccount
     * @param string|null             $customerSoleProprietorPhone
     * @param string|null             $customerSoleProprietorPhoneAdditional
     * @param string|null             $customerSoleProprietorFax
     * @param string|null             $customerSoleProprietorEmail
     * @param string|null             $customerSoleProprietorWebsite
     * @param string|null             $customerJuristicPersonName
     * @param string|null             $customerJuristicPersonInn
     * @param string|null             $customerJuristicPersonKpp
     * @param string|null             $customerJuristicPersonOgrn
     * @param string|null             $customerJuristicPersonOkpo
     * @param string|null             $customerJuristicPersonOkved
     * @param string|null             $customerJuristicPersonLegalAddress
     * @param string|null             $customerJuristicPersonPostalAddress
     * @param string|null             $customerJuristicPersonBankName
     * @param string|null             $customerJuristicPersonBik
     * @param string|null             $customerJuristicPersonCorrespondentAccount
     * @param string|null             $customerJuristicPersonCurrentAccount
     * @param string|null             $customerJuristicPersonPhone
     * @param string|null             $customerJuristicPersonPhoneAdditional
     * @param string|null             $customerJuristicPersonFax
     * @param string|null             $customerJuristicPersonGeneralDirector
     * @param string|null             $customerJuristicPersonEmail
     * @param string|null             $customerJuristicPersonWebsite
     * @param string|null             $burialPlaceOwnerId
     * @param string|null             $burialPlaceOwnerFullName
     * @param string|null             $burialPlaceOwnerPhone
     * @param string|null             $burialPlaceOwnerPhoneAdditional
     * @param string|null             $burialPlaceOwnerEmail
     * @param string|null             $burialPlaceOwnerAddress
     * @param \DateTimeImmutable|null $burialPlaceOwnerBornAt
     * @param string|null             $burialPlaceOwnerPassportSeries
     * @param string|null             $burialPlaceOwnerPassportNumber
     * @param \DateTimeImmutable|null $burialPlaceOwnerPassportIssuedAt
     * @param string|null             $burialPlaceOwnerPassportIssuedBy
     * @param string|null             $burialPlaceOwnerPassportDivisionCode
     * @param string|null             $burialPlaceOwnerPassportPlaceOfBirth
     * @param string|null             $funeralCompanyId
     * @param string|null             $burialChainId
     * @param string|null             $burialPlaceId
     * @param string|null             $burialPlaceType
     * @param string|null             $burialPlaceGeolocationPosition
     * @param string|null             $burialPlaceGraveSiteCemeterySectionId
     * @param string|null             $burialPlaceGraveSiteRowNumber
     * @param string|null             $burialPlaceColumbariumNicheColumbariumId
     * @param string|null             $burialPlaceColumbariumNicheRowNumber
     * @param string|null             $burialPlaceColumbariumNicheNicheNumber
     * @param string|null             $burialPlaceMemorialTreeId
     * @param string|null             $burialContainerId
     * @param string|null             $burialContainerType
     * @param string|null             $burialContainerCoffinSize
     * @param string|null             $burialContainerCoffinIsStandard
     * @param string|null             $burialContainerCoffinType
     * @param \DateTimeImmutable|null $buriedAt
     */
    public function __construct(
        public ?string             $deceasedNaturalPersonId,
        public ?string             $deceasedNaturalPersonFullName,
        public ?\DateTimeImmutable $deceasedNaturalPersonBornAt,
        public \DateTimeImmutable  $deceasedDiedAt,
        public ?string             $deceasedDeathCertificateId,
        public ?string             $deceasedCauseOfDeath,
        public ?string             $customerId,
        public ?string             $customerType,
        public ?string             $customerNaturalPersonFullName,
        public ?string             $customerNaturalPersonPhone,
        public ?string             $customerNaturalPersonPhoneAdditional,
        public ?string             $customerNaturalPersonEmail,
        public ?string             $customerNaturalPersonAddress,
        public ?\DateTimeImmutable $customerNaturalPersonBornAt,
        public ?string             $customerNaturalPersonPlaceOfBirth,
        public ?string             $customerNaturalPersonPassportSeries,
        public ?string             $customerNaturalPersonPassportNumber,
        public ?\DateTimeImmutable $customerNaturalPersonPassportIssuedAt,
        public ?string             $customerNaturalPersonPassportIssuedBy,
        public ?string             $customerNaturalPersonPassportDivisionCode,
        public ?string             $customerSoleProprietorName,
        public ?string             $customerSoleProprietorInn,
        public ?string             $customerSoleProprietorOgrnip,
        public ?string             $customerSoleProprietorOkpo,
        public ?string             $customerSoleProprietorOkved,
        public ?string             $customerSoleProprietorRegistrationAddress,
        public ?string             $customerSoleProprietorActualLocationAddress,
        public ?string             $customerSoleProprietorBankName,
        public ?string             $customerSoleProprietorBik,
        public ?string             $customerSoleProprietorCorrespondentAccount,
        public ?string             $customerSoleProprietorCurrentAccount,
        public ?string             $customerSoleProprietorPhone,
        public ?string             $customerSoleProprietorPhoneAdditional,
        public ?string             $customerSoleProprietorFax,
        public ?string             $customerSoleProprietorEmail,
        public ?string             $customerSoleProprietorWebsite,
        public ?string             $customerJuristicPersonName,
        public ?string             $customerJuristicPersonInn,
        public ?string             $customerJuristicPersonKpp,
        public ?string             $customerJuristicPersonOgrn,
        public ?string             $customerJuristicPersonOkpo,
        public ?string             $customerJuristicPersonOkved,
        public ?string             $customerJuristicPersonLegalAddress,
        public ?string             $customerJuristicPersonPostalAddress,
        public ?string             $customerJuristicPersonBankName,
        public ?string             $customerJuristicPersonBik,
        public ?string             $customerJuristicPersonCorrespondentAccount,
        public ?string             $customerJuristicPersonCurrentAccount,
        public ?string             $customerJuristicPersonPhone,
        public ?string             $customerJuristicPersonPhoneAdditional,
        public ?string             $customerJuristicPersonFax,
        public ?string             $customerJuristicPersonGeneralDirector,
        public ?string             $customerJuristicPersonEmail,
        public ?string             $customerJuristicPersonWebsite,
        public ?string             $burialPlaceOwnerId,
        public ?string             $burialPlaceOwnerFullName,
        public ?string             $burialPlaceOwnerPhone,
        public ?string             $burialPlaceOwnerPhoneAdditional,
        public ?string             $burialPlaceOwnerEmail,
        public ?string             $burialPlaceOwnerAddress,
        public ?\DateTimeImmutable $burialPlaceOwnerBornAt,
        public ?string             $burialPlaceOwnerPassportSeries,
        public ?string             $burialPlaceOwnerPassportNumber,
        public ?\DateTimeImmutable $burialPlaceOwnerPassportIssuedAt,
        public ?string             $burialPlaceOwnerPassportIssuedBy,
        public ?string             $burialPlaceOwnerPassportDivisionCode,
        public ?string             $burialPlaceOwnerPassportPlaceOfBirth,
        public ?string             $funeralCompanyId,
        public ?string             $burialChainId,
        public ?string             $burialPlaceId,
        public ?string             $burialPlaceType,
        public ?string             $burialPlaceGeolocationPosition,
        public ?string             $burialPlaceGraveSiteCemeterySectionId,
        public ?string             $burialPlaceGraveSiteRowNumber,
        public ?string             $burialPlaceColumbariumNicheColumbariumId,
        public ?string             $burialPlaceColumbariumNicheRowNumber,
        public ?string             $burialPlaceColumbariumNicheNicheNumber,
        public ?string             $burialPlaceMemorialTreeId,
        public ?string             $burialContainerId,
        public ?string             $burialContainerType,
        public ?string             $burialContainerCoffinSize,
        public ?string             $burialContainerCoffinIsStandard,
        public ?string             $burialContainerCoffinType,
        public ?\DateTimeImmutable $buriedAt,
    ) {}
}
