<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\CreateFuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateFuneralCompanyRequest
{
    /**
     * @param string|null $funeralCompanyOrganizationId
     * @param string|null $funeralCompanyOrganizationType
     * @param string|null $funeralCompanySoleProprietorName
     * @param string|null $funeralCompanySoleProprietorInn
     * @param string|null $funeralCompanySoleProprietorOgrnip
     * @param string|null $funeralCompanySoleProprietorOkpo
     * @param string|null $funeralCompanySoleProprietorOkved
     * @param string|null $funeralCompanySoleProprietorRegistrationAddress
     * @param string|null $funeralCompanySoleProprietorActualLocationAddress
     * @param string|null $funeralCompanySoleProprietorBankName
     * @param string|null $funeralCompanySoleProprietorBik
     * @param string|null $funeralCompanySoleProprietorCorrespondentAccount
     * @param string|null $funeralCompanySoleProprietorCurrentAccount
     * @param string|null $funeralCompanySoleProprietorPhone
     * @param string|null $funeralCompanySoleProprietorPhoneAdditional
     * @param string|null $funeralCompanySoleProprietorFax
     * @param string|null $funeralCompanySoleProprietorEmail
     * @param string|null $funeralCompanySoleProprietorWebsite
     * @param string|null $funeralCompanyJuristicPersonName
     * @param string|null $funeralCompanyJuristicPersonInn
     * @param string|null $funeralCompanyJuristicPersonKpp
     * @param string|null $funeralCompanyJuristicPersonOgrn
     * @param string|null $funeralCompanyJuristicPersonOkpo
     * @param string|null $funeralCompanyJuristicPersonOkved
     * @param string|null $funeralCompanyJuristicPersonLegalAddress
     * @param string|null $funeralCompanyJuristicPersonPostalAddress
     * @param string|null $funeralCompanyJuristicPersonBankName
     * @param string|null $funeralCompanyJuristicPersonBik
     * @param string|null $funeralCompanyJuristicPersonCorrespondentAccount
     * @param string|null $funeralCompanyJuristicPersonCurrentAccount
     * @param string|null $funeralCompanyJuristicPersonPhone
     * @param string|null $funeralCompanyJuristicPersonPhoneAdditional
     * @param string|null $funeralCompanyJuristicPersonFax
     * @param string|null $funeralCompanyJuristicPersonGeneralDirector
     * @param string|null $funeralCompanyJuristicPersonEmail
     * @param string|null $funeralCompanyJuristicPersonWebsite
     * @param string|null $funeralCompanyNote
     */
    public function __construct(
        public ?string $funeralCompanyOrganizationId,
        public ?string $funeralCompanyOrganizationType,
        public ?string $funeralCompanySoleProprietorName,
        public ?string $funeralCompanySoleProprietorInn,
        public ?string $funeralCompanySoleProprietorOgrnip,
        public ?string $funeralCompanySoleProprietorOkpo,
        public ?string $funeralCompanySoleProprietorOkved,
        public ?string $funeralCompanySoleProprietorRegistrationAddress,
        public ?string $funeralCompanySoleProprietorActualLocationAddress,
        public ?string $funeralCompanySoleProprietorBankName,
        public ?string $funeralCompanySoleProprietorBik,
        public ?string $funeralCompanySoleProprietorCorrespondentAccount,
        public ?string $funeralCompanySoleProprietorCurrentAccount,
        public ?string $funeralCompanySoleProprietorPhone,
        public ?string $funeralCompanySoleProprietorPhoneAdditional,
        public ?string $funeralCompanySoleProprietorFax,
        public ?string $funeralCompanySoleProprietorEmail,
        public ?string $funeralCompanySoleProprietorWebsite,
        public ?string $funeralCompanyJuristicPersonName,
        public ?string $funeralCompanyJuristicPersonInn,
        public ?string $funeralCompanyJuristicPersonKpp,
        public ?string $funeralCompanyJuristicPersonOgrn,
        public ?string $funeralCompanyJuristicPersonOkpo,
        public ?string $funeralCompanyJuristicPersonOkved,
        public ?string $funeralCompanyJuristicPersonLegalAddress,
        public ?string $funeralCompanyJuristicPersonPostalAddress,
        public ?string $funeralCompanyJuristicPersonBankName,
        public ?string $funeralCompanyJuristicPersonBik,
        public ?string $funeralCompanyJuristicPersonCorrespondentAccount,
        public ?string $funeralCompanyJuristicPersonCurrentAccount,
        public ?string $funeralCompanyJuristicPersonPhone,
        public ?string $funeralCompanyJuristicPersonPhoneAdditional,
        public ?string $funeralCompanyJuristicPersonFax,
        public ?string $funeralCompanyJuristicPersonGeneralDirector,
        public ?string $funeralCompanyJuristicPersonEmail,
        public ?string $funeralCompanyJuristicPersonWebsite,
        public ?string $funeralCompanyNote,
    ) {}
}
