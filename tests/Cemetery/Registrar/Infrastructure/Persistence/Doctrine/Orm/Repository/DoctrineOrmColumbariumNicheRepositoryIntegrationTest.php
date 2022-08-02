<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmColumbariumNicheRepository;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumNicheProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumNicheRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = ColumbariumNiche::class;
    protected string $entityIdClassName         = ColumbariumNicheId::class;
    protected string $entityCollectionClassName = ColumbariumNicheCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmColumbariumNicheRepository($this->entityManager);
        $this->entityA = ColumbariumNicheProvider::getColumbariumNicheA();
        $this->entityB = ColumbariumNicheProvider::getColumbariumNicheB();
        $this->entityC = ColumbariumNicheProvider::getColumbariumNicheC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesColumbariumNicheWithSameNumberButAnotherColumbarium(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var ColumbariumNiche $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = new ColumbariumNiche(
            new ColumbariumNicheId('CN00X'),
            new ColumbariumId('C00X'),
            new RowInColumbarium(2),
            $existingEntity->nicheNumber(),
        );
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItSavesColumbariumNicheWithSameNumberAndColumbariumAsRemovedColumbariumNiche(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var ColumbariumNiche $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new ColumbariumNiche(
            new ColumbariumNicheId('NP00X'),
            $entityToRemove->columbariumId(),
            new RowInColumbarium(2),
            $entityToRemove->nicheNumber())
        );
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveColumbariumNicheWithSameNumberAndColumbarium(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var ColumbariumNiche $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = (new ColumbariumNiche(
            new ColumbariumNicheId('NP00X'),
            $existingEntity->columbariumId(),
            new RowInColumbarium(20),
            $existingEntity->nicheNumber())
        );
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Колумбарная ниша с таким номером в этом колумбарии уже существует.');
        $this->repo->save($newEntity);
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var ColumbariumNiche $entityOne */
        /** @var ColumbariumNiche $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->columbariumId(), $entityTwo->columbariumId()) &&
            $this->areEqualValueObjects($entityOne->rowInColumbarium(), $entityTwo->rowInColumbarium()) &&
            $this->areEqualValueObjects($entityOne->nicheNumber(), $entityTwo->nicheNumber()) &&
            $this->areEqualValueObjects($entityOne->geoPosition(), $entityTwo->geoPosition());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newRowInColumbarium = new RowInColumbarium(20);

        /** @var ColumbariumNiche $entityA */
        $entityA->setRowInColumbarium($newRowInColumbarium);
    }
}
