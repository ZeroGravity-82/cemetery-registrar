<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\IdentityGenerator;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialFactory extends EntityFactory
{
    public function __construct(
        private BurialCodeGenerator $burialCodeGenerator,
        IdentityGenerator           $identityGenerator,
    ) {
        parent::__construct($identityGenerator);
    }

    /**
     * @throws Exception when generating an invalid burial ID
     * @throws Exception when generating an invalid burial code
     * @throws Exception when the burial place does not match the burial type
     * @throws Exception when the funeral company not allowed for the burial type
     * @throws Exception when the burial container does not match the burial type
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
