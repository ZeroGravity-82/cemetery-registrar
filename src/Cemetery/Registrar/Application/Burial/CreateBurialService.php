<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCodeGenerator;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheFactory;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteFactory;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedFactory;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Deceased\DeceasedRepository;
use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepository;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorFactory;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurialService extends BurialService
{
    public function __construct(
        private readonly IdentityGenerator          $identityGenerator,
        private readonly BurialCodeGenerator        $burialCodeGenerator,
        private readonly DeceasedFactory            $deceasedFactory,
        private readonly NaturalPersonFactory       $naturalPersonFactory,
        private readonly SoleProprietorFactory      $soleProprietorFactory,
        private readonly JuristicPersonFactory      $juristicPersonFactory,
        private readonly GraveSiteFactory           $graveSiteFactory,
        private readonly ColumbariumNicheFactory    $columbariumNicheFactory,
        private readonly MemorialTreeFactory        $memorialTreeFactory,
        private readonly DeceasedRepository         $deceasedRepo,
        private readonly NaturalPersonRepository    $naturalPersonRepo,
        private readonly SoleProprietorRepository   $soleProprietorRepo,
        private readonly JuristicPersonRepository   $juristicPersonRepo,
        private readonly GraveSiteRepository        $graveSiteRepo,
        private readonly ColumbariumNicheRepository $columbariumNicheRepo,
        private readonly MemorialTreeRepository     $memorialTreeRepo,
        BurialRepository                            $burialRepo,
    ) {
        parent::__construct($burialRepo);
    }

    public function execute(CreateBurialRequest $request): CreateBurialResponse
    {
        $deceasedId         = $this->processDeceasedData($request);
        $burialType         = $this->processBurialTypeData($request);
        $customerId         = $this->processCustomerData($request);
        $burialPlaceId      = $this->processBurialPlaceData($request);
        $burialPlaceOwnerId = $this->processBurialPlaceOwnerData($request);
        $funeralCompanyId   = $this->processFuneralCompanyData($request);
        $burialContainer    = $this->processBurialContainerData($request);
        $buriedAt           = $this->processBuriedAtData($request);

        $burial = (new Burial(
                new BurialId($this->identityGenerator->getNextIdentity()),
                new BurialCode($this->burialCodeGenerator->getNextCode()),
                $deceasedId,
                $burialType,
            ))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBurialContainer($burialContainer)
            ->setBuriedAt($buriedAt);
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
    private function processBurialTypeData(CreateBurialRequest $request): BurialType
    {
        return new BurialType((string) $request->burialType);
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
                NaturalPerson::CLASS_SHORTCUT  => new CustomerId(new NaturalPersonId($request->customerId)),
                SoleProprietor::CLASS_SHORTCUT => new CustomerId(new SoleProprietorId($request->customerId)),
                JuristicPerson::CLASS_SHORTCUT => new CustomerId(new JuristicPersonId($request->customerId)),
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
            $customerId = new CustomerId($customer->id());
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
                GraveSite::CLASS_SHORTCUT        => new BurialPlaceId(new GraveSiteId($request->burialPlaceId)),
                ColumbariumNiche::CLASS_SHORTCUT => new BurialPlaceId(new ColumbariumNicheId($request->burialPlaceId)),
                MemorialTree::CLASS_SHORTCUT     => new BurialPlaceId(new MemorialTreeId($request->burialPlaceId)),
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
            $burialPlaceId = new BurialPlaceId($burialPlace->id());
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
        if ($request->burialPlaceOwnerId === null) {
            $burialPlaceOwner = $this->createNaturalPersonForBurialPlaceOwner($request);
            $this->naturalPersonRepo->save($burialPlaceOwner);
            $burialPlaceOwnerId = $burialPlaceOwner->id();
        }

        return $burialPlaceOwnerId;
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
            $request->deceasedNaturalPersonBornAt,
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
            $request->deceasedDeathCertificateId,
            $request->deceasedCauseOfDeath,
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
            $request->customerNaturalPersonPhone,
            $request->customerNaturalPersonPhoneAdditional,
            $request->customerNaturalPersonEmail,
            $request->customerNaturalPersonAddress,
            $request->customerNaturalPersonBornAt,
            $request->customerNaturalPersonPlaceOfBirth,
            $request->customerNaturalPersonPassportSeries,
            $request->customerNaturalPersonPassportNumber,
            $request->customerNaturalPersonPassportIssuedAt,
            $request->customerNaturalPersonPassportIssuedBy,
            $request->customerNaturalPersonPassportDivisionCode,
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
            $request->customerSoleProprietorBankName,
            $request->customerSoleProprietorBik,
            $request->customerSoleProprietorCorrespondentAccount,
            $request->customerSoleProprietorCurrentAccount,
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
            $request->customerJuristicPersonBankName,
            $request->customerJuristicPersonBik,
            $request->customerJuristicPersonCorrespondentAccount,
            $request->customerJuristicPersonCurrentAccount,
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
            $request->burialPlaceColumbariumNicheRowNumber,
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




    private function assertSupportedCustomerType(CreateBurialRequest $request): void
    {
        if ($request->customerType === null) {
            return;
        }
        if (!\in_array(
            $request->customerType,
            [NaturalPerson::CLASS_SHORTCUT, SoleProprietor::CLASS_SHORTCUT, JuristicPerson::CLASS_SHORTCUT]
        )) {
            throw new \RuntimeException(\sprintf('Неподдерживаемый тип заказчика "%s".', $request->customerType));
        }
    }

    private function assertSupportedBurialPlaceType(CreateBurialRequest $request): void
    {
        if ($request->burialPlaceType === null) {
            return;
        }
        if (!\in_array(
            $request->burialPlaceType,
            [GraveSite::CLASS_SHORTCUT, ColumbariumNiche::CLASS_SHORTCUT, MemorialTree::CLASS_SHORTCUT]
        )) {
            throw new \RuntimeException(\sprintf('Неподдерживаемый тип места захоронения "%s".', $request->burialPlaceType));
        }
    }

    private function assertCustomerTypeProvidedForId(CreateBurialRequest $request): void
    {
        if ($request->customerId !== null && $request->customerType === null) {
            throw new \RuntimeException(
                \sprintf('Не указан тип заказчика для идентификатора "%s".', $request->customerId)
            );
        }
    }

    private function assertBurialPlaceTypeProvidedForId(CreateBurialRequest $request): void
    {
        if ($request->burialPlaceId !== null && $request->burialPlaceType === null) {
            throw new \RuntimeException(
                \sprintf('Не указан тип места захоронения для идентификатора "%s".', $request->burialPlaceId)
            );
        }
    }

}
