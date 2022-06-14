<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\EntityFactory;
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
     * @param DeceasedId              $deceasedId
     * @param CustomerId|null         $customerId
     * @param BurialPlaceId|null      $burialPlaceId
     * @param NaturalPersonId|null    $burialPlaceOwnerId
     * @param FuneralCompanyId|null   $funeralCompanyId
     * @param BurialContainer|null    $burialContainer
     * @param \DateTimeImmutable|null $buriedAt
     *
     * @return Burial
     */
    public function create(
        BurialType          $type,
        DeceasedId          $deceasedId,
        ?CustomerId         $customerId,
        ?BurialPlaceId      $burialPlaceId,
        ?NaturalPersonId    $burialPlaceOwnerId,
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
            ->setBurialPlaceOwnerId($burialPlaceOwnerId)
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBurialContainer($burialContainer)
            ->setBuriedAt($buriedAt);
    }
}
