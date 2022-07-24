<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmColumbariumRepository;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = Columbarium::class;
    protected string $entityIdClassName         = ColumbariumId::class;
    protected string $entityCollectionClassName = ColumbariumCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmColumbariumRepository($this->entityManager);
        $this->entityA = ColumbariumProvider::getColumbariumA();
        $this->entityB = ColumbariumProvider::getColumbariumB();
        $this->entityC = ColumbariumProvider::getColumbariumC();
    }

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(Columbarium::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(ColumbariumId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(ColumbariumCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    public function testItChecksThatSameNameAlreadyUsed(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $mockEntityWithSameName = $this->createMock(Columbarium::class);
        $mockEntityWithSameName->method('name')->willReturn($this->entityA->name());
        $this->assertTrue($this->repo->doesSameNameAlreadyUsed($mockEntityWithSameName));
    }

    public function testItDoesNotConsiderNameUsedByProvidedColumbariumItself(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $this->assertFalse($this->repo->doesSameNameAlreadyUsed($this->entityA));
    }

    public function testItDoesNotConsiderNameUsedByRemovedColumbarium(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $this->repo->remove($persistedEntityB);
        $this->entityManager->clear();

        $this->assertFalse($this->repo->doesSameNameAlreadyUsed($persistedEntityB));
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var Columbarium $entityOne */
        /** @var Columbarium $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->areEqualValueObjects($entityOne->geoPosition(), $entityTwo->geoPosition());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newName = new ColumbariumName('западный 2');

        /** @var Columbarium $entityA */
        $entityA->setName($newName);
    }
}
