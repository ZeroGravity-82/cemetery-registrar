<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateBurial;

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
     * @param string|null             $customerNaturalPersonPassportSeries
     * @param string|null             $customerNaturalPersonPassportNumber
     * @param \DateTimeImmutable|null $customerNaturalPersonPassportIssuedAt
     * @param string|null             $customerNaturalPersonPassportIssuedBy
     * @param string|null             $customerNaturalPersonPassportDivisionCode
     * @param string|null             $customerNaturalPersonPassportPlaceOfBirth
     * @param string|null             $customerSoleProprietorFullName
     * @param string|null             $customerSoleProprietorInn
     * @param string|null             $customerSoleProprietorOgrnip
     * @param string|null             $customerSoleProprietorRegistrationAddress
     * @param string|null             $customerSoleProprietorActualLocationAddress
     * @param string|null             $customerSoleProprietorBankName
     * @param string|null             $customerSoleProprietorRcbic
     * @param string|null             $customerSoleProprietorCorrespondentAccount
     * @param string|null             $customerSoleProprietorCurrentAccount
     * @param string|null             $customerSoleProprietorPhone
     * @param string|null             $customerSoleProprietorPhoneAdditional
     * @param string|null             $customerSoleProprietorEmail
     * @param string|null             $customerSoleProprietorFax
     * @param string|null             $customerSoleProprietorWebsite
     * @param string|null             $customerJuristicPersonName
     * @param string|null             $customerJuristicPersonInn
     * @param string|null             $customerJuristicPersonKpp
     * @param string|null             $customerJuristicPersonOgrn
     * @param string|null             $customerJuristicPersonLegalAddress
     * @param string|null             $customerJuristicPersonPostalAddress
     * @param string|null             $customerJuristicPersonBankName
     * @param string|null             $customerJuristicPersonRcbic
     * @param string|null             $customerJuristicPersonCorrespondentAccount
     * @param string|null             $customerJuristicPersonCurrentAccount
     * @param string|null             $customerJuristicPersonPhone
     * @param string|null             $customerJuristicPersonPhoneAdditional
     * @param string|null             $customerJuristicPersonEmail
     * @param string|null             $customerJuristicPersonFax
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
     * @param string|null             $funeralCompanyType
     * @param string|null             $burialChainId
     * @param string|null             $burialPlaceId
     * @param string|null             $burialPlaceType
     * @param string|null             $burialPlacePosition
     * @param string|null             $burialPlaceGraveSiteCemeterySection
     * @param string|null             $burialPlaceGraveSiteRowNumber
     * @param string|null             $burialPlaceColumbariumNicheColumbariumNumber
     * @param string|null             $burialPlaceColumbariumNicheRowNumber
     * @param string|null             $burialPlaceColumbariumNicheNicheNumber
     * @param string|null             $burialPlaceMemorialTreeNumber
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
        public ?string             $customerNaturalPersonPassportSeries,
        public ?string             $customerNaturalPersonPassportNumber,
        public ?\DateTimeImmutable $customerNaturalPersonPassportIssuedAt,
        public ?string             $customerNaturalPersonPassportIssuedBy,
        public ?string             $customerNaturalPersonPassportDivisionCode,
        public ?string             $customerNaturalPersonPassportPlaceOfBirth,
        public ?string             $customerSoleProprietorFullName,
        public ?string             $customerSoleProprietorInn,
        public ?string             $customerSoleProprietorOgrnip,
        public ?string             $customerSoleProprietorRegistrationAddress,
        public ?string             $customerSoleProprietorActualLocationAddress,
        public ?string             $customerSoleProprietorBankName,
        public ?string             $customerSoleProprietorRcbic,
        public ?string             $customerSoleProprietorCorrespondentAccount,
        public ?string             $customerSoleProprietorCurrentAccount,
        public ?string             $customerSoleProprietorPhone,
        public ?string             $customerSoleProprietorPhoneAdditional,
        public ?string             $customerSoleProprietorEmail,
        public ?string             $customerSoleProprietorFax,
        public ?string             $customerSoleProprietorWebsite,
        public ?string             $customerJuristicPersonName,
        public ?string             $customerJuristicPersonInn,
        public ?string             $customerJuristicPersonKpp,
        public ?string             $customerJuristicPersonOgrn,
        public ?string             $customerJuristicPersonLegalAddress,
        public ?string             $customerJuristicPersonPostalAddress,
        public ?string             $customerJuristicPersonBankName,
        public ?string             $customerJuristicPersonRcbic,
        public ?string             $customerJuristicPersonCorrespondentAccount,
        public ?string             $customerJuristicPersonCurrentAccount,
        public ?string             $customerJuristicPersonPhone,
        public ?string             $customerJuristicPersonPhoneAdditional,
        public ?string             $customerJuristicPersonEmail,
        public ?string             $customerJuristicPersonFax,
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
        public ?string             $funeralCompanyType,
        public ?string             $burialChainId,
        public ?string             $burialPlaceId,
        public ?string             $burialPlaceType,
        public ?string             $burialPlacePosition,
        public ?string             $burialPlaceGraveSiteCemeterySection,
        public ?string             $burialPlaceGraveSiteRowNumber,
        public ?string             $burialPlaceColumbariumNicheColumbariumNumber,
        public ?string             $burialPlaceColumbariumNicheRowNumber,
        public ?string             $burialPlaceColumbariumNicheNicheNumber,
        public ?string             $burialPlaceMemorialTreeNumber,
        public ?string             $burialContainerId,
        public ?string             $burialContainerType,
        public ?string             $burialContainerCoffinSize,
        public ?string             $burialContainerCoffinIsStandard,
        public ?string             $burialContainerCoffinType,
        public ?\DateTimeImmutable $buriedAt,
    ) {}
}