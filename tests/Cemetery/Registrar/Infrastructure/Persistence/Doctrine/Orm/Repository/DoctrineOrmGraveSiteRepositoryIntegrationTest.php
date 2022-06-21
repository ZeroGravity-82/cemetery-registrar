<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmGraveSiteRepository($this->entityManager);
        $this->entityA = GraveSiteProvider::getGraveSiteA();
        $this->entityB = GraveSiteProvider::getGraveSiteB();
        $this->entityC = GraveSiteProvider::getGraveSiteC();
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
