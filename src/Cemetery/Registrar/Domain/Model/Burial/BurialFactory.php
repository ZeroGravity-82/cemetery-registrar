<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\AbstractEntityFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\AbstractBurialPlace;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialFactory extends AbstractEntityFactory
{
    public function __construct(
        IdentityGeneratorInterface           $identityGenerator,
        private BurialCodeGeneratorInterface $burialCodeGenerator,
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
        BurialType                                       $type,
        NaturalPersonId                                  $deceasedId,
        NaturalPerson|JuristicPerson|SoleProprietor|null $customer,
        ?AbstractBurialPlace                             $burialPlace,
        ?FuneralCompanyId                                $funeralCompanyId,
        ?BurialContainer                                 $burialContainer,
        ?\DateTimeImmutable                              $buriedAt,
    ): Burial {
        $burial = (new Burial(
            new BurialId($this->identityGenerator->getNextIdentity()),
            new BurialCode($this->burialCodeGenerator->getNextCode()),
            $type,
            $deceasedId,
        ))
            ->setFuneralCompanyId($funeralCompanyId)
            ->setBurialContainer($burialContainer)
            ->setBuriedAt($buriedAt);
        if ($customer) {
            $burial->assignCustomer($customer);
        }
        if ($burialPlace) {
            $burial->assignBurialPlace($burialPlace);
        }

        return $burial;
    }
}
