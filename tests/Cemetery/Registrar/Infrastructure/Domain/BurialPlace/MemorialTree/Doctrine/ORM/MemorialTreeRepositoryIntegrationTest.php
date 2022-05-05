<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\BurialPlace\MemorialTree\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeNumber;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\MemorialTree\Doctrine\ORM\MemorialTreeRepository as DoctrineOrmMemorialTreeRepository;
use Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeRepositoryIntegrationTest extends RepositoryIntegrationTest
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
