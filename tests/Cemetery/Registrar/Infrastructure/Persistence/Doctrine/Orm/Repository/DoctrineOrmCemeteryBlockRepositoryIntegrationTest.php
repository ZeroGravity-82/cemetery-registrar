<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryValidator;
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

        $this->mockRepositoryValidator = $this->createMock(CemeteryBlockRepositoryValidator::class);
        $this->repo                    = new DoctrineOrmCemeteryBlockRepository(
            $this->entityManager,
            $this->mockRepositoryValidator,
        );
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

    public function testItChecksThatSameNameAlreadyUsed(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new CemeteryBlockCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $mockEntityWithSameName = $this->createMock(CemeteryBlock::class);
        $mockEntityWithSameName->method('name')->willReturn($this->entityA->name());
        $this->assertTrue($this->repo->doesSameNameAlreadyUsed($mockEntityWithSameName));
    }

    public function testItDoesNotConsiderNameUsedByProvidedCemeteryBlockItself(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new CemeteryBlockCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $this->assertFalse($this->repo->doesSameNameAlreadyUsed($this->entityA));
    }

    public function testItDoesNotConsiderNameUsedByRemovedCemeteryBlock(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new CemeteryBlockCollection([
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
