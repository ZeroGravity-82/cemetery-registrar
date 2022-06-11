<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Domain\Entity;
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

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var MemorialTree $entityOne */
        /** @var MemorialTree $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->treeNumber(), $entityTwo->treeNumber()) &&
            $this->areEqualValueObjects($entityOne->geoPosition(), $entityTwo->geoPosition());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newTreeNumber = new MemorialTreeNumber('005');

        /** @var MemorialTree $entityA */
        $entityA->setTreeNumber($newTreeNumber);
    }
}
