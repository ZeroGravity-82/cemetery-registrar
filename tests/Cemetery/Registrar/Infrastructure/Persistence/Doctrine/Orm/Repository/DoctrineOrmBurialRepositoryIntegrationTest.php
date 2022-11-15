<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\AbstractEntity;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmBurialRepository;
use DataFixtures\Burial\BurialProvider;
use DataFixtures\Organization\SoleProprietor\SoleProprietorProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepositoryIntegrationTest extends AbstractDoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = Burial::class;
    protected string $entityIdClassName         = BurialId::class;
    protected string $entityCollectionClassName = BurialCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmBurialRepository($this->entityManager);
        $this->entityA = BurialProvider::getBurialA();
        $this->entityB = BurialProvider::getBurialB();
        $this->entityC = BurialProvider::getBurialC();
    }

    protected function areEqualEntities(AbstractEntity $entityOne, AbstractEntity $entityTwo): bool
    {
        /** @var Burial $entityOne */
        /** @var Burial $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->code(), $entityTwo->code()) &&
            $this->areEqualValueObjects($entityOne->deceasedId(), $entityTwo->deceasedId()) &&
            $this->areEqualValueObjects($entityOne->type(), $entityTwo->type()) &&
            $this->areEqualValueObjects($entityOne->customerId(), $entityTwo->customerId()) &&
            $this->areEqualValueObjects($entityOne->burialPlaceId(), $entityTwo->burialPlaceId()) &&
            $this->areEqualValueObjects($entityOne->funeralCompanyId(), $entityTwo->funeralCompanyId()) &&
            $this->areEqualValueObjects($entityOne->burialContainer(), $entityTwo->burialContainer()) &&
            $this->areEqualDateTimeValues($entityOne->buriedAt(), $entityTwo->buriedAt());
    }

    protected function updateEntityA(AbstractEntity $entityA): void
    {
        $newCustomer = SoleProprietorProvider::getSoleProprietorA();

        /** @var Burial $entityA */
        $entityA->assignCustomer($newCustomer);
    }
}
