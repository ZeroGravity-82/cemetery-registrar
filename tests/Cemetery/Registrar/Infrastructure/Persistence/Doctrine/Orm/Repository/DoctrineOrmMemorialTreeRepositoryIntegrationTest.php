<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmMemorialTreeRepository;
use DataFixtures\BurialPlace\MemorialTree\MemorialTreeProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmMemorialTreeRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = MemorialTree::class;
    protected string $entityIdClassName         = MemorialTreeId::class;
    protected string $entityCollectionClassName = MemorialTreeCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmMemorialTreeRepository($this->entityManager);
        $this->entityA = MemorialTreeProvider::getMemorialTreeA();
        $this->entityB = MemorialTreeProvider::getMemorialTreeB();
        $this->entityC = MemorialTreeProvider::getMemorialTreeC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesMemorialTreeWithSameNumberAsRemovedMemorialTree(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var MemorialTree $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = new MemorialTree(new MemorialTreeId('MT00X'), $entityToRemove->treeNumber());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveMemorialTreeWithSameNumber(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var MemorialTree $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = new MemorialTree(new MemorialTreeId('MT00X'), $existingEntity->treeNumber());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Памятное дерево с таким номером уже существует.');
        $this->repo->save($newEntity);
    }
    
    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var MemorialTree $entityOne */
        /** @var MemorialTree $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->treeNumber(), $entityTwo->treeNumber()) &&
            $this->areEqualValueObjects($entityOne->personInChargeId(), $entityTwo->personInChargeId()) &&
            $this->areEqualValueObjects($entityOne->geoPosition(), $entityTwo->geoPosition());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newTreeNumber = new MemorialTreeNumber('005');

        /** @var MemorialTree $entityA */
        $entityA->setTreeNumber($newTreeNumber);
    }
}
