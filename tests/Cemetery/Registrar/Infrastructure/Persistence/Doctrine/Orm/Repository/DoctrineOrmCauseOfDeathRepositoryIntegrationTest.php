<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmCauseOfDeathRepository;
use DataFixtures\CauseOfDeath\CauseOfDeathProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCauseOfDeathRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = CauseOfDeath::class;
    protected string $entityIdClassName         = CauseOfDeathId::class;
    protected string $entityCollectionClassName = CauseOfDeathCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmCauseOfDeathRepository($this->entityManager);
        $this->entityA = CauseOfDeathProvider::getCauseOfDeathA();
        $this->entityB = CauseOfDeathProvider::getCauseOfDeathB();
        $this->entityC = CauseOfDeathProvider::getCauseOfDeathC();
    }

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(CauseOfDeath::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(CauseOfDeathId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(CauseOfDeathCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var CauseOfDeath $entityOne */
        /** @var CauseOfDeath $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newName = new CauseOfDeathName('COVID19');

        /** @var CauseOfDeath $entityA */
        $entityA->setName($newName);
    }
}
