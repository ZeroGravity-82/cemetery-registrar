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

        $this->repo = new DoctrineOrmCauseOfDeathRepository($this->entityManager);
        $this->entityA = CauseOfDeathProvider::getCauseOfDeathA();
        $this->entityB = CauseOfDeathProvider::getCauseOfDeathB();
        $this->entityC = CauseOfDeathProvider::getCauseOfDeathC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesCauseOfDeathWithSameNameAsRemovedCauseOfDeath(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var CauseOfDeath $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = new CauseOfDeath(new CauseOfDeathId('CD00X'), $entityToRemove->name());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveCauseOfDeathWithSameName(): void
    {
        // Prepare the repo for testing
        /** @var CauseOfDeath $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = new CauseOfDeath(new CauseOfDeathId('CD00X'), $existingEntity->name());
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Причина смерти с таким наименованием уже существует.');
        $this->repo->save($newEntity);
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
        $newName = new CauseOfDeathName('COVID-18');

        /** @var CauseOfDeath $entityA */
        $entityA->setName($newName);
    }
}
