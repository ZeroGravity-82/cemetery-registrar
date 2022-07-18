<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepositoryValidator;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\Model\Entity;
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

    private ColumbariumNiche $entityD;
    private ColumbariumNiche $entityE;
    private ColumbariumNiche $entityF;
    private ColumbariumNiche $entityG;
    private ColumbariumNiche $entityH;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepositoryValidator = $this->createMock(ColumbariumNicheRepositoryValidator::class);
        $this->repo                    = new DoctrineOrmColumbariumNicheRepository(
            $this->entityManager,
            $this->mockRepositoryValidator,
        );
        $this->entityA = ColumbariumNicheProvider::getColumbariumNicheA();
        $this->entityB = ColumbariumNicheProvider::getColumbariumNicheB();
        $this->entityC = ColumbariumNicheProvider::getColumbariumNicheC();
        $this->entityD = ColumbariumNicheProvider::getColumbariumNicheD();
        $this->entityE = ColumbariumNicheProvider::getColumbariumNicheE();
        $this->entityF = ColumbariumNicheProvider::getColumbariumNicheF();
        $this->entityG = ColumbariumNicheProvider::getColumbariumNicheG();
        $this->entityH = ColumbariumNicheProvider::getColumbariumNicheH();
    }

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(ColumbariumNiche::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(ColumbariumNicheId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(ColumbariumNicheCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    public function testItCountsColumbariumNichesByColumbariumId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownColumbariumId    = new ColumbariumId('C004');
        $columbariumNicheCount = $this->repo->countByColumbariumId($knownColumbariumId);
        $this->assertSame(5, $columbariumNicheCount);

        $unknownColumbariumId  = new ColumbariumId('unknown_id');
        $columbariumNicheCount = $this->repo->countByColumbariumId($unknownColumbariumId);
        $this->assertSame(0, $columbariumNicheCount);
    }

    public function testItDoesNotCountRemovedColumbariumNichesByColumbariumId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityF = $this->repo->findById($this->entityF->id());
        $columbariumIdF   = $persistedEntityF->columbariumId();
        $this->repo->remove($persistedEntityF);
        $this->entityManager->clear();

        $columbariumNicheCount = $this->repo->countByColumbariumId($columbariumIdF);
        $this->assertSame(4, $columbariumNicheCount);
    }

    public function testItChecksThatSameNicheNumberAlreadyUsed(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $mockEntityWithSameNicheNumber = $this->createMock(ColumbariumNiche::class);
        $mockEntityWithSameNicheNumber->method('columbariumId')->willReturn($this->entityA->columbariumId());
        $mockEntityWithSameNicheNumber->method('nicheNumber')->willReturn($this->entityA->nicheNumber());
        $this->assertTrue($this->repo->doesSameNicheNumberAlreadyUsed($mockEntityWithSameNicheNumber));
    }

    public function testItDoesNotConsiderNicheNumberUsedByProvidedColumbariumNicheItself(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $this->assertFalse($this->repo->doesSameNicheNumberAlreadyUsed($this->entityA));
    }

    public function testItDoesNotConsiderNicheNumberUsedFromAnotherColumbarium(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $this->assertFalse($this->repo->doesSameNicheNumberAlreadyUsed($this->entityH));
    }

    public function testItDoesNotConsiderNicheNumberUsedByRemovedColumbariumNiche(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $this->repo->remove($persistedEntityB);
        $this->entityManager->clear();

        $this->assertFalse($this->repo->doesSameNicheNumberAlreadyUsed($persistedEntityB));
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
