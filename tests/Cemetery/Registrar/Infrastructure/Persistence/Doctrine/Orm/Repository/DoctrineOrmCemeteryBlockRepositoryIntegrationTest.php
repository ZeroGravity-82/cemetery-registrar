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

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(CemeteryBlock::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(CemeteryBlockId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(CemeteryBlockCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
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
