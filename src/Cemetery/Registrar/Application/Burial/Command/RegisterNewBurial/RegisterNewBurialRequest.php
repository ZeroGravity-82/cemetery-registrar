<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Command\RegisterNewBurial;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RegisterNewBurialRequest extends ApplicationRequest
{
    public ?string $type = null;
    public ?string $deceasedNaturalPersonId = null;
    public ?string $deceasedNaturalPersonFullName = null;
    public ?string $deceasedNaturalPersonBornAt = null;
    public ?string $deceasedDiedAt = null;
    public ?int    $deceasedAge = null;
    public ?string $deceasedDeathCertificateId = null;
    public ?string $deceasedCauseOfDeathId = null;
    public ?string $customerId = null;
    public ?string $customerType = null;
    public ?string $customerNaturalPersonFullName = null;
    public ?string $customerNaturalPersonPhone = null;
    public ?string $customerNaturalPersonPhoneAdditional = null;
    public ?string $customerNaturalPersonEmail = null;
    public ?string $customerNaturalPersonAddress = null;
    public ?string $customerNaturalPersonBornAt = null;
    public ?string $customerNaturalPersonPlaceOfBirth = null;
    public ?string $customerNaturalPersonPassportSeries = null;
    public ?string $customerNaturalPersonPassportNumber = null;
    public ?string $customerNaturalPersonPassportIssuedAt = null;
    public ?string $customerNaturalPersonPassportIssuedBy = null;
    public ?string $customerNaturalPersonPassportDivisionCode = null;
    public ?string $customerSoleProprietorName = null;
    public ?string $customerSoleProprietorInn = null;
    public ?string $customerSoleProprietorOgrnip = null;
    public ?string $customerSoleProprietorOkpo = null;
    public ?string $customerSoleProprietorOkved = null;
    public ?string $customerSoleProprietorRegistrationAddress = null;
    public ?string $customerSoleProprietorActualLocationAddress = null;
    public ?string $customerSoleProprietorBankDetailsBankName = null;
    public ?string $customerSoleProprietorBankDetailsBik = null;
    public ?string $customerSoleProprietorBankDetailsCorrespondentAccount = null;
    public ?string $customerSoleProprietorBankDetailsCurrentAccount = null;
    public ?string $customerSoleProprietorPhone = null;
    public ?string $customerSoleProprietorPhoneAdditional = null;
    public ?string $customerSoleProprietorFax = null;
    public ?string $customerSoleProprietorEmail = null;
    public ?string $customerSoleProprietorWebsite = null;
    public ?string $customerJuristicPersonName = null;
    public ?string $customerJuristicPersonInn = null;
    public ?string $customerJuristicPersonKpp = null;
    public ?string $customerJuristicPersonOgrn = null;
    public ?string $customerJuristicPersonOkpo = null;
    public ?string $customerJuristicPersonOkved = null;
    public ?string $customerJuristicPersonLegalAddress = null;
    public ?string $customerJuristicPersonPostalAddress = null;
    public ?string $customerJuristicPersonBankDetailsBankName = null;
    public ?string $customerJuristicPersonBankDetailsBik = null;
    public ?string $customerJuristicPersonBankDetailsCorrespondentAccount = null;
    public ?string $customerJuristicPersonBankDetailsCurrentAccount = null;
    public ?string $customerJuristicPersonPhone = null;
    public ?string $customerJuristicPersonPhoneAdditional = null;
    public ?string $customerJuristicPersonFax = null;
    public ?string $customerJuristicPersonGeneralDirector = null;
    public ?string $customerJuristicPersonEmail = null;
    public ?string $customerJuristicPersonWebsite = null;
    public ?string $personInChargeId = null;
    public ?string $personInChargeFullName = null;
    public ?string $personInChargePhone = null;
    public ?string $personInChargePhoneAdditional = null;
    public ?string $personInChargeEmail = null;
    public ?string $personInChargeAddress = null;
    public ?string $personInChargeBornAt = null;
    public ?string $personInChargePlaceOfBirth = null;
    public ?string $personInChargePassportSeries = null;
    public ?string $personInChargePassportNumber = null;
    public ?string $personInChargePassportIssuedAt = null;
    public ?string $personInChargePassportIssuedBy = null;
    public ?string $personInChargePassportDivisionCode = null;
    public ?string $funeralCompanyId = null;
    public ?string $burialChainId = null;
    public ?string $burialPlaceId = null;
    public ?string $burialPlaceType = null;
    public ?string $burialPlaceGraveSiteCemeteryBlockId = null;
    public ?int    $burialPlaceGraveSiteRowInBlock = null;
    public ?int    $burialPlaceGraveSitePositionInRow = null;
    public ?string $burialPlaceGraveSiteSize = null;
    public ?string $burialPlaceColumbariumNicheColumbariumId = null;
    public ?int    $burialPlaceColumbariumNicheRowInColumbarium = null;
    public ?string $burialPlaceColumbariumNicheNicheNumber = null;
    public ?string $burialPlaceMemorialTreeNumber = null;
    public ?string $burialPlaceGeoPositionLatitude = null;
    public ?string $burialPlaceGeoPositionLongitude = null;
    public ?string $burialPlaceGeoPositionError = null;
    public ?string $burialContainerType = null;
    public ?int    $burialContainerCoffinSize = null;
    public ?string $burialContainerCoffinShape = null;
    public ?bool   $burialContainerCoffinIsNonStandard = null;
    public ?string $buriedAt = null;
}
