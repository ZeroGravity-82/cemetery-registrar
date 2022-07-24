<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmGraveSiteRepository;
use DataFixtures\BurialPlace\GraveSite\GraveSiteProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmGraveSiteRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = GraveSite::class;
    protected string $entityIdClassName         = GraveSiteId::class;
    protected string $entityCollectionClassName = GraveSiteCollection::class;

    private GraveSite $entityD;
    private GraveSite $entityE;
    private GraveSite $entityF;
    private GraveSite $entityG;
    private GraveSite $entityH;
    private GraveSite $entityI;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmGraveSiteRepository($this->entityManager);
        $this->entityA = GraveSiteProvider::getGraveSiteA();
        $this->entityB = GraveSiteProvider::getGraveSiteB();
        $this->entityC = GraveSiteProvider::getGraveSiteC();
        $this->entityD = GraveSiteProvider::getGraveSiteD();
        $this->entityE = GraveSiteProvider::getGraveSiteE();
        $this->entityF = GraveSiteProvider::getGraveSiteF();
        $this->entityG = GraveSiteProvider::getGraveSiteG();
        $this->entityH = GraveSiteProvider::getGraveSiteH();
        $this->entityI = GraveSiteProvider::getGraveSiteI();
    }

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(GraveSite::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(GraveSiteId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(GraveSiteCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    public function testItCountsGraveSitesByCemeteryBlockId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new GraveSiteCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
            $this->entityI,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownCemeteryBlockId = new CemeteryBlockId('CB004');
        $graveSiteCount       = $this->repo->countByCemeteryBlockId($knownCemeteryBlockId);
        $this->assertSame(3, $graveSiteCount);

        $unknownCemeteryBlockId = new CemeteryBlockId('unknown_id');
        $graveSiteCount         = $this->repo->countByCemeteryBlockId($unknownCemeteryBlockId);
        $this->assertSame(0, $graveSiteCount);
    }

    public function testItDoesNotCountRemovedGraveSitesByCemeteryBlockId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new GraveSiteCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
            $this->entityI,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityF = $this->repo->findById($this->entityF->id());
        $cemeteryBlockIdF = $persistedEntityF->cemeteryBlockId();
        $this->repo->remove($persistedEntityF);
        $this->entityManager->clear();

        $graveSiteCount = $this->repo->countByCemeteryBlockId($cemeteryBlockIdF);
        $this->assertSame(2, $graveSiteCount);
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var GraveSite $entityOne */
        /** @var GraveSite $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->cemeteryBlockId(), $entityTwo->cemeteryBlockId()) &&
            $this->areEqualValueObjects($entityOne->rowInBlock(), $entityTwo->rowInBlock()) &&
            $this->areEqualValueObjects($entityOne->positionInRow(), $entityTwo->positionInRow()) &&
            $this->areEqualValueObjects($entityOne->geoPosition(), $entityTwo->geoPosition()) &&
            $this->areEqualValueObjects($entityOne->size(), $entityTwo->size());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newSize = new GraveSiteSize('2.0');

        /** @var GraveSite $entityA */
        $entityA->setSize($newSize);
    }
}
