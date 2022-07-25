<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmCemeteryBlockRepository;
use DataFixtures\BurialPlace\GraveSite\CemeteryBlockProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCemeteryBlockRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = CemeteryBlock::class;
    protected string $entityIdClassName         = CemeteryBlockId::class;
    protected string $entityCollectionClassName = CemeteryBlockCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmCemeteryBlockRepository($this->entityManager);
        $this->entityA = CemeteryBlockProvider::getCemeteryBlockA();
        $this->entityB = CemeteryBlockProvider::getCemeteryBlockB();
        $this->entityC = CemeteryBlockProvider::getCemeteryBlockC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesCemeteryBlockWithSameNameAsRemovedCemeteryBlock(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var CemeteryBlock $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = new CemeteryBlock(new CemeteryBlockId('CB00X'), $entityToRemove->name());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveCemeteryBlockWithSameName(): void
    {
        // Prepare the repo for testing
        /** @var CemeteryBlock $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = new CemeteryBlock(new CemeteryBlockId('CB00X'), $existingEntity->name());
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Квартал с таким наименованием уже существует.');
        $this->repo->save($newEntity);
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var CemeteryBlock $entityOne */
        /** @var CemeteryBlock $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newName = new CemeteryBlockName('общий квартал В');

        /** @var CemeteryBlock $entityA */
        $entityA->setName($newName);
    }
}
