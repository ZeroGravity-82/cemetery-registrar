<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM\ColumbariumRepository as DoctrineOrmColumbariumRepository;
use Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
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

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var Columbarium $entityOne */
        /** @var Columbarium $entityTwo */
        $isSameClass = $entityOne instanceof Columbarium && $entityTwo instanceof Columbarium;


        // Mandatory properties
        $isSameId   = $entityOne->id()->isEqual($entityTwo->id());
        $isSameName = $entityOne->name()->isEqual($entityTwo->name());

        // Optional properties
        $isSameGeoPosition = $entityOne->geoPosition() !== null && $entityTwo->geoPosition() !== null
            ? $entityOne->geoPosition()->isEqual($entityTwo->geoPosition())
            : $entityOne->geoPosition() === null && $entityTwo->geoPosition() === null;

        return
            $isSameClass && $isSameId && $isSameName && $isSameGeoPosition;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newName        = new ColumbariumName('западный колумбарий 2');
        $newGeoPosition = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);

        /** @var Columbarium $entityA */
        $entityA->setName($newName);
        $entityA->setGeoPosition($newGeoPosition);
    }
}
