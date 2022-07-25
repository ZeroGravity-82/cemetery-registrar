<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmBurialRepository;
use DataFixtures\Burial\BurialProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
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

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
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
            $this->areEqualValueObjects($entityOne->personInChargeId(), $entityTwo->personInChargeId()) &&
            $this->areEqualValueObjects($entityOne->funeralCompanyId(), $entityTwo->funeralCompanyId()) &&
            $this->areEqualValueObjects($entityOne->burialContainer(), $entityTwo->burialContainer()) &&
            $this->areEqualDateTimeValues($entityOne->buriedAt(), $entityTwo->buriedAt());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newCustomerId = new CustomerId(new SoleProprietorId('SP001'));

        /** @var Burial $entityA */
        $entityA->setCustomerId($newCustomerId);
    }
}
