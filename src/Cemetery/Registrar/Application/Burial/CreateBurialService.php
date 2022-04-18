<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

use Cemetery\Registrar\Domain\Burial\BurialBuilder;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedBuilder;
use Cemetery\Registrar\Domain\Deceased\DeceasedRepositoryInterface;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonFactory;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorFactory;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurialService extends BurialService
{
    public function __construct(
        private readonly BurialBuilder                     $burialBuilder,
        private readonly DeceasedBuilder                   $deceasedBuilder,
        private readonly NaturalPersonFactory              $naturalPersonFactory,
        private readonly SoleProprietorFactory             $soleProprietorFactory,
        private readonly JuristicPersonFactory             $juristicPersonFactory,
        private readonly GraveSiteFactory                  $graveSiteFactory,
        private readonly ColumbariumNicheFactory           $columbariumNicheFactory,
        private readonly MemorialTreeFactory               $memorialTreeFactory,


        private readonly DeceasedRepositoryInterface       $deceasedRepo,
        private readonly NaturalPersonRepositoryInterface  $naturalPersonRepo,
        private readonly SoleProprietorRepositoryInterface $soleProprietorRepo,
        private readonly JuristicPersonRepositoryInterface     $juristicPersonRepo,
        private readonly GraveSiteRepositoryInterface          $graveSiteRepo,
        private readonly ColumbariumNicheRepositoryInterface   $columbariumNicheRepo,
        private readonly MemorialTreeRepositoryInterface       $memorialTreeRepo,


    ) {}

    public function execute(CreateBurialRequest $request): CreateBurialResponse
    {
        $this->processDeceasedData($request, $this->burialBuilder);
        $this->processCustomerData($request, $this->burialBuilder);
        $this->processBurialPlaceData($request, $this->burialBuilder);
        $this->processBurialPlaceOwnerData($request, $this->burialBuilder);
        $this->processFuneralCompanyData($request, $this->burialBuilder);
        $this->processBurialContainerData($request, $this->burialBuilder);
        $this->processBuriedAtData($request, $this->burialBuilder);

        $burial = $this->burialBuilder->getResult();
        $this->burailRepo->save($burial);

        return new CreateBurialResponse((string) $burial->getId());
    }


    /**
     * @param CreateBurialRequest $request
     * @param BurialBuilder       $burialBuilder
     */
    private function processDeceasedData(CreateBurialRequest $request, BurialBuilder $burialBuilder): void
    {
        $this->assertValidDeceasedData($request);
        $naturalPersonId                 = $request->deceasedNaturalPersonId;
        $isNaturalPersonCreationRequired = !$naturalPersonId;
        if ($isNaturalPersonCreationRequired) {
            $naturalPerson   = $this->createNaturalPersonForDeceased($request);
            $naturalPersonId = (string) $naturalPerson->getId();
        }
        $deceased = $this->createDeceased($naturalPersonId, $request);
        $burialBuilder->initialize($deceased->getId());
    }

    /**
     * @param CreateBurialRequest $request
     * @param BurialBuilder       $burialBuilder
     */
    private function processCustomerData(CreateBurialRequest $request, BurialBuilder $burialBuilder): void
    {
        $this->assertValidCustomerData($request);
        $customerId                 = $request->customerId;
        $isCustomerCreationRequired = !$customerId && $request->customerType;
        if ($isCustomerCreationRequired) {
            $customer   = $this->createCustomer($request);
            $customerId = (string) $customer->getId();
        }
        if ($customerId) {
            $customerType = new CustomerType($request->customerType);
            $customerId   = new CustomerId($customerId, $customerType);
            $burialBuilder->addCustomerId($customerId);
        }
    }

    private function processBurialPlaceData(CreateBurialRequest $request, BurialBuilder $burialBuilder): void
    {
        $this->assertValidBurialPlaceData($request);
        $burialPlaceId                 = $request->burialPlaceId;
        $burialPlaceType               = $request->burialPlaceType;
        $isBurialPlaceCreationRequired = !$burialPlaceId && $burialPlaceType;
        if ($isBurialPlaceCreationRequired) {
            $burialPlace   = $this->createBurialPlace($request);
            $burialPlaceId = (string) $burialPlace->getId();
        }
        if ($burialPlaceId) {
            $burialPlaceType = new BurialPlaceType($burialPlaceType);
            $burialPlaceId   = new BurialPlaceId($burialPlaceId, $burialPlaceType);
            $burialBuilder->addBurialPlaceId($burialPlaceId);
        }
    }





    /**
     * @param CreateBurialRequest $request
     *
     * @return NaturalPerson
     */
    private function createNaturalPersonForDeceased(CreateBurialRequest $request): NaturalPerson
    {
        $naturalPerson = $this->naturalPersonFactory->createForDeceased(
            $request->deceasedNaturalPersonFullName,
            $request->deceasedNaturalPersonBornAt,
        );
        $this->naturalPersonRepo->save($naturalPerson);

        return $naturalPerson;
    }

    /**
     * @param string              $naturalPersonId
     * @param CreateBurialRequest $request
     *
     * @return Deceased
     */
    private function createDeceased(string $naturalPersonId, CreateBurialRequest $request): Deceased
    {
        $deceased = $this->deceasedBuilder
            ->initialize($naturalPersonId, $request->deceasedDiedAt)
            ->addDeathCertificateId($request->deceasedDeathCertificateId)
            ->addCauseOfDeath($request->deceasedCauseOfDeath)
            ->build();
        $this->deceasedRepo->save($deceased);

        return $deceased;
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return NaturalPerson|SoleProprietor|JuristicPerson
     *
     * @throws \InvalidArgumentException when the provided customer type is unsupported
     */
    private function createCustomer(CreateBurialRequest $request): NaturalPerson|SoleProprietor|JuristicPerson
    {
        return match ($request->customerType) {
            CustomerType::NATURAL_PERSON  => $this->createNaturalPersonForCustomer($request),
            CustomerType::SOLE_PROPRIETOR => $this->createSoleProprietorForCustomer($request),
            CustomerType::JURISTIC_PERSON => $this->createJuristicPersonForCustomer($request),
            default                       => throw new \InvalidArgumentException(
                \sprintf('Неподдерживаемый тип заказчика захоронения "%s".', $request->customerType)
            ),
        };
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return NaturalPerson
     */
    private function createNaturalPersonForCustomer(CreateBurialRequest $request): NaturalPerson
    {
        $naturalPerson = $this->naturalPersonFactory->createForBurialCustomer(
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
        $this->naturalPersonRepo->save($naturalPerson);

        return $naturalPerson;
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return SoleProprietor
     */
    private function createSoleProprietorForCustomer(CreateBurialRequest $request): SoleProprietor
    {
        $soleProprietor = $this->soleProprietorFactory->create(
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
        $this->soleProprietorRepo->save($soleProprietor);

        return $soleProprietor;
    }

    /**
     * @param CreateBurialRequest $request
     *
     * @return JuristicPerson
     */
    private function createJuristicPersonForCustomer(CreateBurialRequest $request): JuristicPerson
    {
        $juristicPerson = $this->juristicPersonFactory->create(
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
        $this->juristicPersonRepo->save($juristicPerson);

        return $juristicPerson;
    }




    /**
     * @param CreateBurialRequest $request
     *
     * @return GraveSite|ColumbariumNiche|MemorialTree
     *
     * @throws \InvalidArgumentException when the provided burial place type is unsupported
     */
    private function createBurialPlace(CreateBurialRequest $request): GraveSite|ColumbariumNiche|MemorialTree
    {
        switch ($request->burialPlaceType) {
            case BurialPlaceType::GRAVE_SITE:
                $burialPlace = $this->graveSiteFactory->create(

                );
                $this->graveSiteRepo->save($burialPlace);
                break;
            case BurialPlaceType::COLUMBARIUM_NICHE:
                $burialPlace = $this->columbariumNicheFactory->create(

                );
                $this->columbariumNicheRepo->save($burialPlace);
                break;
            case BurialPlaceType::MEMORIAL_TREE:
                $burialPlace = $this->memorialTreeFactory->create(

                );
                $this->memorialTreeRepo->save($burialPlace);
                break;
            default:
                throw new \InvalidArgumentException(
                    \sprintf('Unsupported burial place type "%s".', $request->burialPlaceType)
                );
        }

        return $burialPlace;
    }






    private function assertValidDeceasedData(CreateBurialRequest $request): void
    {
        $isDataComplete = $request->deceasedNaturalPersonId ||
            $request->deceasedNaturalPersonFullName;
        if (!$isDataComplete) {
            throw new \InvalidArgumentException('The data of the deceased is incomplete.');
        }
    }

    private function assertValidCustomerData(CreateBurialRequest $request): void
    {
        $isDataComplete = $request->customerId ||
            $request->customerType;
        if (!$isDataComplete) {
            throw new \InvalidArgumentException('The data of the customer is incomplete.');
        }
    }

    private function assertValidBurialPlaceData(CreateBurialRequest $request): void
    {
        $isDataComplete = $request->burialPlaceId ||
            $request->burialPlaceType;
        if (!$isDataComplete) {
            throw new \InvalidArgumentException('The data of the burial place is incomplete.');
        }
    }


}
