<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryValidator;
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

        $this->mockRepositoryValidator = $this->createMock(CauseOfDeathRepositoryValidator::class);
        $this->repo                    = new DoctrineOrmCauseOfDeathRepository(
            $this->entityManager,
            $this->mockRepositoryValidator,
        );
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

    public function testItChecksThatSameNameAlreadyUsed(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new CauseOfDeathCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $mockEntityWithSameName = $this->createMock(CauseOfDeath::class);
        $mockEntityWithSameName->method('name')->willReturn($this->entityA->name());
        $this->assertTrue($this->repo->doesSameNameAlreadyUsed($mockEntityWithSameName));
    }

    public function testItDoesNotConsiderNameUsedByProvidedCauseOfDeathItself(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new CauseOfDeathCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $this->assertFalse($this->repo->doesSameNameAlreadyUsed($this->entityA));
    }

    public function testItDoesNotConsiderNameUsedByRemovedCauseOfDeath(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new CauseOfDeathCollection([
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
