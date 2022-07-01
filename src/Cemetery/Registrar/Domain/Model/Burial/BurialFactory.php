<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\IdentityGenerator;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialFactory extends EntityFactory
{
    /**
     * @param BurialCodeGenerator $burialCodeGenerator
     * @param IdentityGenerator   $identityGenerator
     */
    public function __construct(
        private readonly BurialCodeGenerator $burialCodeGenerator,
        IdentityGenerator                    $identityGenerator,
    ) {
        parent::__construct($identityGenerator);
    }

    /**
     * @param BurialType              $type
     * @param NaturalPersonId         $deceasedId
     * @param CustomerId|null         $customerId
     * @param BurialPlaceId|null      $burialPlaceId
     * @param NaturalPersonId|null    $personInChargeId
     * @param FuneralCompanyId|null   $funeralCompanyId
     * @param BurialContainer|null    $burialContainer
     * @param \DateTimeImmutable|null $buriedAt
     *
     * @return Burial
     */
    public function create(
        BurialType          $type,
        NaturalPersonId     $deceasedId,
        ?CustomerId         $customerId,
        ?BurialPlaceId      $burialPlaceId,
        ?NaturalPersonId    $personInChargeId,
        ?FuneralCompanyId   $funeralCompanyId,
        ?BurialContainer    $burialContainer,
        ?\DateTimeImmutable $buriedAt,
    ): Burial {
        return (new Burial(
            new BurialId($this->identityGenerator->getNextIdentity()),
            new BurialCode($this->burialCodeGenerator->getNextCode()),
            $type,
            $deceasedId,
        ))
            ->setCustomerId($customerId)
            ->setBurialPlaceId($burialPlaceId)
            ->setPersonInChargeId($personInChargeId)
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBurialContainer($burialContainer)
            ->setBuriedAt($buriedAt);
    }
}
