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
