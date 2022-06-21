<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
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

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmColumbariumNicheRepository($this->entityManager);
        $this->entityA = ColumbariumNicheProvider::getColumbariumNicheA();
        $this->entityB = ColumbariumNicheProvider::getColumbariumNicheB();
        $this->entityC = ColumbariumNicheProvider::getColumbariumNicheC();
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
