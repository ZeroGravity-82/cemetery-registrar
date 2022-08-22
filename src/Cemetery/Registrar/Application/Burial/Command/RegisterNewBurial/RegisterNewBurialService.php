<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Command\RegisterNewBurial;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainerFactory;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\Burial\BurialFactory;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceIdFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonFactory;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorFactory;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RegisterNewBurialService extends ApplicationService
{
    public function __construct(
        private readonly BurialRepository           $burialRepo,
        private readonly BurialFactory              $burialFactory,
        private readonly NaturalPersonFactory       $naturalPersonFactory,
        private readonly SoleProprietorFactory      $soleProprietorFactory,
        private readonly JuristicPersonFactory      $juristicPersonFactory,
        private readonly GraveSiteFactory           $graveSiteFactory,
        private readonly ColumbariumNicheFactory    $columbariumNicheFactory,
        private readonly MemorialTreeFactory        $memorialTreeFactory,
        private readonly BurialContainerFactory     $burialContainerFactory,
        private readonly BurialPlaceIdFactory       $burialPlaceIdFactory,
        private readonly NaturalPersonRepository    $naturalPersonRepo,
        private readonly SoleProprietorRepository   $soleProprietorRepo,
        private readonly JuristicPersonRepository   $juristicPersonRepo,
        private readonly GraveSiteRepository        $graveSiteRepo,
        private readonly ColumbariumNicheRepository $columbariumNicheRepo,
        private readonly MemorialTreeRepository     $memorialTreeRepo,
        RegisterNewBurialRequestValidator           $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $type             = $this->processTypeData($request);
        $deceasedId       = $this->processDeceasedData($request);
        $customerId       = $this->processCustomerData($request);
        $burialPlaceId    = $this->processBurialPlaceData($request);
        $personInChargeId = $this->processPersonInChargeData($request);
        $funeralCompanyId = $this->processFuneralCompanyData($request);
        $burialContainer  = $this->processBurialContainerData($request);
        $buriedAt         = $this->processBuriedAtData($request);

        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            $customerId,
            $burialPlaceId,
            $personInChargeId,
            $funeralCompanyId,
            $burialContainer,
            $buriedAt,
        );
        $this->burialRepo->save($burial);

        return new RegisterNewBurialResponse($burial->id()->value());
    }

    protected function supportedRequestClassName(): string
    {
        return RegisterNewBurialRequest::class;
    }

    private function processDeceasedData(RegisterNewBurialRequest $request): DeceasedId
    {
        $deceasedNaturalPersonId = $request->deceasedNaturalPersonId;
        if ($deceasedNaturalPersonId === null) {
            $deceasedNaturalPerson = $this->createNaturalPersonForDeceased($request);
            $this->naturalPersonRepo->save($deceasedNaturalPerson);
            $deceasedNaturalPersonId = $deceasedNaturalPerson->id()->value();
        }
        $deceased = $this->createDeceased($request, $deceasedNaturalPersonId);
        $this->deceasedRepo->save($deceased);

        return $deceased->id();
    }

    private function processTypeData(RegisterNewBurialRequest $request): BurialType
    {
        return new BurialType((string) $request->type);
    }

    private function processCustomerData(RegisterNewBurialRequest $request): ?CustomerId
    {
        $customerId = null;

        $this->assertSupportedCustomerType($request);
        $this->assertCustomerTypeProvidedForId($request);
        if ($request->customerId !== null && $request->customerType !== null) {
            $customerId = match ($request->customerType) {
                NaturalPerson::CLASS_SHORTCUT  =>
                    $this->customerIdFactory->createForNaturalPerson($request->customerId),
                SoleProprietor::CLASS_SHORTCUT =>
                    $this->customerIdFactory->createForSoleProprietor($request->customerId),
                JuristicPerson::CLASS_SHORTCUT =>
                    $this->customerIdFactory->createForJuristicPerson($request->customerId),
            };
        }
        if ($request->customerId === null && $request->customerType !== null) {
            switch ($request->customerType) {
                case NaturalPerson::CLASS_SHORTCUT:
                    $customer = $this->createNaturalPersonForCustomer($request);
                    $this->naturalPersonRepo->save($customer);
                    break;
                case SoleProprietor::CLASS_SHORTCUT:
                    $customer = $this->createSoleProprietorForCustomer($request);
                    $this->soleProprietorRepo->save($customer);
                    break;
                case JuristicPerson::CLASS_SHORTCUT:
                    $customer = $this->createJuristicPersonForCustomer($request);
                    $this->juristicPersonRepo->save($customer);
                    break;
            }
            /** @var NaturalPerson|SoleProprietor|JuristicPerson $customer */
            $customerId = $this->customerIdFactory->create($customer->id());
        }

        return $customerId;
    }

    private function processBurialPlaceData(RegisterNewBurialRequest $request): ?BurialPlaceId
    {
        $burialPlaceId = null;

        $this->assertSupportedBurialPlaceType($request);
        $this->assertBurialPlaceTypeProvidedForId($request);
        if ($request->burialPlaceId !== null && $request->burialPlaceType !== null) {
            $burialPlaceId = match ($request->burialPlaceType) {
                GraveSite::CLASS_SHORTCUT        =>
                    $this->burialPlaceIdFactory->createForGraveSite($request->burialPlaceId),
                ColumbariumNiche::CLASS_SHORTCUT =>
                    $this->burialPlaceIdFactory->createForColumbariumNiche($request->burialPlaceId),
                MemorialTree::CLASS_SHORTCUT     =>
                    $this->burialPlaceIdFactory->createForMemorialTree($request->burialPlaceId),
            };
        }
        if ($request->burialPlaceId === null && $request->burialPlaceType !== null) {
            switch ($request->burialPlaceType) {
                case GraveSite::CLASS_SHORTCUT:
                    $burialPlace = $this->createGraveSite($request);
                    $this->graveSiteRepo->save($burialPlace);
                    break;
                case ColumbariumNiche::CLASS_SHORTCUT:
                    $burialPlace = $this->createColumbariumNiche($request);
                    $this->columbariumNicheRepo->save($burialPlace);
                    break;
                case MemorialTree::CLASS_SHORTCUT:
                    $burialPlace = $this->createMemorialTree($request);
                    $this->memorialTreeRepo->save($burialPlace);
                    break;
            }
            /** @var GraveSite|ColumbariumNiche|MemorialTree $burialPlace */
            $burialPlaceId = $this->burialPlaceIdFactory->create($burialPlace->id());
        }

        return $burialPlaceId;
    }

    private function processPersonInChargeData(RegisterNewBurialRequest $request): ?NaturalPersonId
    {
        $personInChargeId = null;

        if ($request->personInChargeId !== null) {
            $personInChargeId = new NaturalPersonId($request->personInChargeId);
        }
//        if ($request->personInChargeId === null) {
//            $personInCharge = $this->createNaturalPersonForPersonInCharge($request);
//            $this->naturalPersonRepo->save($personInCharge);
//            $personInChargeId = $personInCharge->id();
//        }

        return $personInChargeId;
    }

    private function processFuneralCompanyData(RegisterNewBurialRequest $request): ?FuneralCompanyId
    {
        $funeralCompanyId = null;

        if ($request->funeralCompanyId !== null) {
            $funeralCompanyId = new FuneralCompanyId($request->funeralCompanyId);
        }

        return $funeralCompanyId;
    }

    private function processBurialContainerData(RegisterNewBurialRequest $request): ?BurialContainer
    {
        $burialContainer = null;

        $this->assertSupportedBurialContainerType($request);
        if ($request->burialContainerType !== null) {
            $burialContainer = match ($request->burialContainerType) {
                Coffin::CLASS_SHORTCUT => $this->burialContainerFactory->createForCoffin(
                    $request->burialContainerCoffinSize,
                    $request->burialContainerCoffinShape,
                    $request->burialContainerCoffinIsNonStandard,
                ),
                Urn::CLASS_SHORTCUT => $this->burialContainerFactory->createForUrn(),
            };
        }

        return $burialContainer;
    }

    private function processBuriedAtData(RegisterNewBurialRequest $request): ?\DateTimeImmutable
    {
        $buriedAt = null;

        if (!empty($request->buriedAt)) {
            $buriedAt = new \DateTimeImmutable($request->buriedAt);
        }

        return $buriedAt;
    }

    private function createNaturalPersonForDeceased(RegisterNewBurialRequest $request): NaturalPerson
    {
        return $this->naturalPersonFactory->create(
            $request->deceasedNaturalPersonFullName,
            null,
            null,
            null,
            null,
            $request->deceasedNaturalPersonBornAt ?: null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
    }

    private function createDeceased(RegisterNewBurialRequest $request, string $naturalPersonId): Deceased
    {
        return $this->deceasedFactory->create(
            $naturalPersonId,
            $request->deceasedDiedAt,
            $request->deceasedAge                ?: null,
            $request->deceasedDeathCertificateId ?: null,
            $request->deceasedCauseOfDeathId     ?: null,
        );
    }

    private function createNaturalPersonForCustomer(RegisterNewBurialRequest $request): NaturalPerson
    {
        return $this->naturalPersonFactory->create(
            $request->customerNaturalPersonFullName,
            $request->customerNaturalPersonPhone                ?: null,
            $request->customerNaturalPersonPhoneAdditional      ?: null,
            $request->customerNaturalPersonEmail                ?: null,
            $request->customerNaturalPersonAddress              ?: null,
            $request->customerNaturalPersonBornAt               ?: null,
            $request->customerNaturalPersonPlaceOfBirth         ?: null,
            $request->customerNaturalPersonPassportSeries       ?: null,
            $request->customerNaturalPersonPassportNumber       ?: null,
            $request->customerNaturalPersonPassportIssuedAt     ?: null,
            $request->customerNaturalPersonPassportIssuedBy     ?: null,
            $request->customerNaturalPersonPassportDivisionCode ?: null,
        );
    }

    private function createSoleProprietorForCustomer(RegisterNewBurialRequest $request): SoleProprietor
    {
        return $this->soleProprietorFactory->create(
            $request->customerSoleProprietorName,
            $request->customerSoleProprietorInn,
            $request->customerSoleProprietorOgrnip,
            $request->customerSoleProprietorOkpo,
            $request->customerSoleProprietorOkved,
            $request->customerSoleProprietorRegistrationAddress,
            $request->customerSoleProprietorActualLocationAddress,
            $request->customerSoleProprietorBankDetailsBankName,
            $request->customerSoleProprietorBankDetailsBik,
            $request->customerSoleProprietorBankDetailsCorrespondentAccount,
            $request->customerSoleProprietorBankDetailsCurrentAccount,
            $request->customerSoleProprietorPhone,
            $request->customerSoleProprietorPhoneAdditional,
            $request->customerSoleProprietorFax,
            $request->customerSoleProprietorEmail,
            $request->customerSoleProprietorWebsite,
        );
    }

    private function createJuristicPersonForCustomer(RegisterNewBurialRequest $request): JuristicPerson
    {
        return $this->juristicPersonFactory->create(
            $request->customerJuristicPersonName,
            $request->customerJuristicPersonInn,
            $request->customerJuristicPersonKpp,
            $request->customerJuristicPersonOgrn,
            $request->customerJuristicPersonOkpo,
            $request->customerJuristicPersonOkved,
            $request->customerJuristicPersonLegalAddress,
            $request->customerJuristicPersonPostalAddress,
            $request->customerJuristicPersonBankDetailsBankName,
            $request->customerJuristicPersonBankDetailsBik,
            $request->customerJuristicPersonBankDetailsCorrespondentAccount,
            $request->customerJuristicPersonBankDetailsCurrentAccount,
            $request->customerJuristicPersonPhone,
            $request->customerJuristicPersonPhoneAdditional,
            $request->customerJuristicPersonFax,
            $request->customerJuristicPersonGeneralDirector,
            $request->customerJuristicPersonEmail,
            $request->customerJuristicPersonWebsite,
        );
    }

    private function createGraveSite(RegisterNewBurialRequest $request): GraveSite
    {
        return $this->graveSiteFactory->create(
            $request->burialPlaceGraveSiteCemeteryBlockId,
            $request->burialPlaceGraveSiteRowInBlock,
            $request->burialPlaceGraveSitePositionInRow,
            $request->burialPlaceGeoPositionLatitude,
            $request->burialPlaceGeoPositionLongitude,
            $request->burialPlaceGeoPositionError,
            $request->burialPlaceGraveSiteSize,
        );
    }

    private function createColumbariumNiche(RegisterNewBurialRequest $request): ColumbariumNiche
    {
        return $this->columbariumNicheFactory->create(
            $request->burialPlaceColumbariumNicheColumbariumId,
            $request->burialPlaceColumbariumNicheRowInColumbarium,
            $request->burialPlaceColumbariumNicheNicheNumber,
            $request->burialPlaceGeoPositionLatitude,
            $request->burialPlaceGeoPositionLongitude,
            $request->burialPlaceGeoPositionError,
        );
    }

    private function createMemorialTree(RegisterNewBurialRequest $request): MemorialTree
    {
        return $this->memorialTreeFactory->create(
            $request->burialPlaceMemorialTreeNumber,
            $request->burialPlaceGeoPositionLatitude,
            $request->burialPlaceGeoPositionLongitude,
            $request->burialPlaceGeoPositionError,
        );
    }

    private function createNaturalPersonForPersonInCharge(RegisterNewBurialRequest $request): NaturalPerson
    {
        return $this->naturalPersonFactory->create(
            $request->personInChargeFullName,
            $request->personInChargePhone,
            $request->personInChargePhoneAdditional,
            $request->personInChargeEmail,
            $request->personInChargeAddress,
            $request->personInChargeBornAt,
            $request->personInChargePlaceOfBirth,
            $request->personInChargePassportSeries,
            $request->personInChargePassportNumber,
            $request->personInChargePassportIssuedAt,
            $request->personInChargePassportIssuedBy,
            $request->personInChargePassportDivisionCode,
        );
    }

    private function assertSupportedCustomerType(RegisterNewBurialRequest $request): void
    {
        if ($request->customerType === null) {
            return;
        }
        if (!\in_array(
            $request->customerType,
            [NaturalPerson::CLASS_SHORTCUT, SoleProprietor::CLASS_SHORTCUT, JuristicPerson::CLASS_SHORTCUT]
        )) {
            throw new \LogicException(
                \sprintf('Неподдерживаемый тип заказчика "%s".', $request->customerType)
            );
        }
    }

    private function assertSupportedBurialPlaceType(RegisterNewBurialRequest $request): void
    {
        if ($request->burialPlaceType === null) {
            return;
        }
        if (!\in_array(
            $request->burialPlaceType,
            [GraveSite::CLASS_SHORTCUT, ColumbariumNiche::CLASS_SHORTCUT, MemorialTree::CLASS_SHORTCUT]
        )) {
            throw new \LogicException(
                \sprintf('Неподдерживаемый тип места захоронения "%s".', $request->burialPlaceType)
            );
        }
    }

    private function assertSupportedBurialContainerType(RegisterNewBurialRequest $request): void
    {
        if ($request->burialContainerType === null) {
            return;
        }
        if (!\in_array(
            $request->burialContainerType,
            [Coffin::CLASS_SHORTCUT, Urn::CLASS_SHORTCUT]
        )) {
            throw new \LogicException(
                \sprintf('Неподдерживаемый тип контейнера захоронения "%s".', $request->burialContainerType)
            );
        }
    }

    private function assertCustomerTypeProvidedForId(RegisterNewBurialRequest $request): void
    {
        if ($request->customerId !== null && $request->customerType === null) {
            throw new \LogicException(
                \sprintf('Не указан тип заказчика для идентификатора "%s".', $request->customerId)
            );
        }
    }

    private function assertBurialPlaceTypeProvidedForId(RegisterNewBurialRequest $request): void
    {
        if ($request->burialPlaceId !== null && $request->burialPlaceType === null) {
            throw new \LogicException(
                \sprintf('Не указан тип места захоронения для идентификатора "%s".', $request->burialPlaceId)
            );
        }
    }
}
