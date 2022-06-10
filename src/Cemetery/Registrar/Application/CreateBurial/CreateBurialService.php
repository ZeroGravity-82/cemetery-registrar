<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CreateBurial;

use Cemetery\Registrar\Domain\Burial\BurialFactory;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceIdFactory;
use Cemetery\Registrar\Domain\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerIdFactory;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainerFactory;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheFactory;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteFactory;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeFactory;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedFactory;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Deceased\DeceasedRepository;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepository;
use Cemetery\Registrar\Domain\Organization\OrganizationIdFactory;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorFactory;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateBurialService
{
    public function __construct(
        private readonly BurialRepository           $burialRepo,
        private readonly BurialFactory              $burialFactory,
        private readonly DeceasedFactory            $deceasedFactory,
        private readonly NaturalPersonFactory       $naturalPersonFactory,
        private readonly SoleProprietorFactory      $soleProprietorFactory,
        private readonly JuristicPersonFactory      $juristicPersonFactory,
        private readonly GraveSiteFactory           $graveSiteFactory,
        private readonly ColumbariumNicheFactory    $columbariumNicheFactory,
        private readonly MemorialTreeFactory        $memorialTreeFactory,
        private readonly BurialContainerFactory     $burialContainerFactory,
        private readonly DeceasedRepository         $deceasedRepo,
        private readonly BurialPlaceIdFactory       $burialPlaceIdFactory,
        private readonly OrganizationIdFactory      $organizationIdFactory,
        private readonly CustomerIdFactory          $customerIdFactory,
        private readonly NaturalPersonRepository    $naturalPersonRepo,
        private readonly SoleProprietorRepository   $soleProprietorRepo,
        private readonly JuristicPersonRepository   $juristicPersonRepo,
        private readonly GraveSiteRepository        $graveSiteRepo,
        private readonly ColumbariumNicheRepository $columbariumNicheRepo,
        private readonly MemorialTreeRepository     $memorialTreeRepo,
    ) {}

    /**
     * @param CreateBurialRequest $request
     *
     * @return CreateBurialResponse
     */
    public function execute($request): CreateBurialResponse
    {
        $type               = $this->processTypeData($request);
        $deceasedId         = $this->processDeceasedData($request);
        $customerId         = $this->processCustomerData($request);
        $burialPlaceId      = $this->processBurialPlaceData($request);
        $burialPlaceOwnerId = $this->processBurialPlaceOwnerData($request);
        $funeralCompanyId   = $this->processFuneralCompanyData($request);
        $burialContainer    = $this->processBurialContainerData($request);
        $buriedAt           = $this->processBuriedAtData($request);

        $burial = $this->burialFactory->create(
            $type,
            $deceasedId,
            $customerId,
            $burialPlaceId,
            $burialPlaceOwnerId,
            $funeralCompanyId,
            $burialContainer,
            $buriedAt,
        );
        $this->burialRepo->save($burial);

        return new CreateBurialResponse($burial->id()->value());
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return DeceasedId
     */
    private function processDeceasedData(CreateBurialRequest $request): DeceasedId
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return BurialType
     */
    private function processTypeData(CreateBurialRequest $request): BurialType
    {
        return new BurialType((string) $request->type);
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return CustomerId|null
     */
    private function processCustomerData(CreateBurialRequest $request): ?CustomerId
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return BurialPlaceId|null
     */
    private function processBurialPlaceData(CreateBurialRequest $request): ?BurialPlaceId
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return NaturalPersonId|null
     */
    private function processBurialPlaceOwnerData(CreateBurialRequest $request): ?NaturalPersonId
    {
        $burialPlaceOwnerId = null;

        if ($request->burialPlaceOwnerId !== null) {
            $burialPlaceOwnerId = new NaturalPersonId($request->burialPlaceOwnerId);
        }
//        if ($request->burialPlaceOwnerId === null) {
//            $burialPlaceOwner = $this->createNaturalPersonForBurialPlaceOwner($request);
//            $this->naturalPersonRepo->save($burialPlaceOwner);
//            $burialPlaceOwnerId = $burialPlaceOwner->id();
//        }

        return $burialPlaceOwnerId;
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return FuneralCompanyId|null
     */
    private function processFuneralCompanyData(CreateBurialRequest $request): ?FuneralCompanyId
    {
        $funeralCompanyId = null;

        if ($request->funeralCompanyId !== null) {
            $funeralCompanyId = new FuneralCompanyId($request->funeralCompanyId);
        }

        return $funeralCompanyId;
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return BurialContainer|null
     */
    private function processBurialContainerData(CreateBurialRequest $request): ?BurialContainer
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return \DateTimeImmutable|null
     */
    private function processBuriedAtData(CreateBurialRequest $request): ?\DateTimeImmutable
    {
        $buriedAt = null;

        if (!empty($request->buriedAt)) {
            $buriedAt = new \DateTimeImmutable($request->buriedAt);
        }

        return $buriedAt;
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return NaturalPerson
     */
    private function createNaturalPersonForDeceased(CreateBurialRequest $request): NaturalPerson
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

    /**
     * @param CreateBurialRequest $request
     * @param string              $naturalPersonId
     *
     * @return Deceased
     */
    private function createDeceased(CreateBurialRequest $request, string $naturalPersonId): Deceased
    {
        return $this->deceasedFactory->create(
            $naturalPersonId,
            $request->deceasedDiedAt,
            $request->deceasedAge                ?: null,
            $request->deceasedDeathCertificateId ?: null,
            $request->deceasedCauseOfDeath       ?: null,
        );
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return NaturalPerson
     */
    private function createNaturalPersonForCustomer(CreateBurialRequest $request): NaturalPerson
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return SoleProprietor
     */
    private function createSoleProprietorForCustomer(CreateBurialRequest $request): SoleProprietor
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return JuristicPerson
     */
    private function createJuristicPersonForCustomer(CreateBurialRequest $request): JuristicPerson
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return GraveSite
     */
    private function createGraveSite(CreateBurialRequest $request): GraveSite
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return ColumbariumNiche
     */
    private function createColumbariumNiche(CreateBurialRequest $request): ColumbariumNiche
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

    /**
     * @param CreateBurialRequest $request
     *
     * @return MemorialTree
     */
    private function createMemorialTree(CreateBurialRequest $request): MemorialTree
    {
        return $this->memorialTreeFactory->create(
            $request->burialPlaceMemorialTreeNumber,
            $request->burialPlaceGeoPositionLatitude,
            $request->burialPlaceGeoPositionLongitude,
            $request->burialPlaceGeoPositionError,
        );
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return NaturalPerson
     */
    private function createNaturalPersonForBurialPlaceOwner(CreateBurialRequest $request): NaturalPerson
    {
        return $this->naturalPersonFactory->create(
            $request->burialPlaceOwnerFullName,
            $request->burialPlaceOwnerPhone,
            $request->burialPlaceOwnerPhoneAdditional,
            $request->burialPlaceOwnerEmail,
            $request->burialPlaceOwnerAddress,
            $request->burialPlaceOwnerBornAt,
            $request->burialPlaceOwnerPlaceOfBirth,
            $request->burialPlaceOwnerPassportSeries,
            $request->burialPlaceOwnerPassportNumber,
            $request->burialPlaceOwnerPassportIssuedAt,
            $request->burialPlaceOwnerPassportIssuedBy,
            $request->burialPlaceOwnerPassportDivisionCode,
        );
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @throws \RuntimeException when the customer type is not supported
     */
    private function assertSupportedCustomerType(CreateBurialRequest $request): void
    {
        if ($request->customerType === null) {
            return;
        }
        if (!\in_array(
            $request->customerType,
            [NaturalPerson::CLASS_SHORTCUT, SoleProprietor::CLASS_SHORTCUT, JuristicPerson::CLASS_SHORTCUT]
        )) {
            throw new \RuntimeException(
                \sprintf('Неподдерживаемый тип заказчика "%s".', $request->customerType)
            );
        }
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @throws \RuntimeException when the burial place type is not supported
     */
    private function assertSupportedBurialPlaceType(CreateBurialRequest $request): void
    {
        if ($request->burialPlaceType === null) {
            return;
        }
        if (!\in_array(
            $request->burialPlaceType,
            [GraveSite::CLASS_SHORTCUT, ColumbariumNiche::CLASS_SHORTCUT, MemorialTree::CLASS_SHORTCUT]
        )) {
            throw new \RuntimeException(
                \sprintf('Неподдерживаемый тип места захоронения "%s".', $request->burialPlaceType)
            );
        }
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @throws \RuntimeException when the burial container type is not supported
     */
    private function assertSupportedBurialContainerType(CreateBurialRequest $request): void
    {
        if ($request->burialContainerType === null) {
            return;
        }
        if (!\in_array(
            $request->burialContainerType,
            [Coffin::CLASS_SHORTCUT, Urn::CLASS_SHORTCUT]
        )) {
            throw new \RuntimeException(
                \sprintf('Неподдерживаемый тип контейнера захоронения "%s".', $request->burialContainerType)
            );
        }
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @throws \RuntimeException when the customer type is not provided for the customer ID
     */
    private function assertCustomerTypeProvidedForId(CreateBurialRequest $request): void
    {
        if ($request->customerId !== null && $request->customerType === null) {
            throw new \RuntimeException(
                \sprintf('Не указан тип заказчика для идентификатора "%s".', $request->customerId)
            );
        }
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @throws \RuntimeException when the burial place type is not provided for the burial place ID
     */
    private function assertBurialPlaceTypeProvidedForId(CreateBurialRequest $request): void
    {
        if ($request->burialPlaceId !== null && $request->burialPlaceType === null) {
            throw new \RuntimeException(
                \sprintf('Не указан тип места захоронения для идентификатора "%s".', $request->burialPlaceId)
            );
        }
    }
}
