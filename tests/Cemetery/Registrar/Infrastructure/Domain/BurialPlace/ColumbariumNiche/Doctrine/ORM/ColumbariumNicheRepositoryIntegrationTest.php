<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM\ColumbariumNicheRepository as DoctrineOrmColumbariumNicheRepository;
use Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheRepositoryIntegrationTest extends RepositoryIntegrationTest
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

    protected function checkAreEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var ColumbariumNiche $entityOne */
        /** @var ColumbariumNiche $entityTwo */
        return
            $this->checkAreSameClasses($entityOne, $entityTwo) &&
            $this->checkAreEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->checkAreEqualValueObjects($entityOne->columbariumId(), $entityTwo->columbariumId()) &&
            $this->checkAreEqualValueObjects($entityOne->rowInColumbarium(), $entityTwo->rowInColumbarium()) &&
            $this->checkAreEqualValueObjects($entityOne->nicheNumber(), $entityTwo->nicheNumber()) &&
            $this->checkAreEqualValueObjects($entityOne->geoPosition(), $entityTwo->geoPosition());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newColumbariumId    = new ColumbariumId('C020');
        $newRowInColumbarium = new RowInColumbarium(20);
        $newNicheNumber      = new ColumbariumNicheNumber('020');
        $newGeoPosition      = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);

        /** @var ColumbariumNiche $entityA */
        $entityA->setColumbariumId($newColumbariumId);
        $entityA->setRowInColumbarium($newRowInColumbarium);
        $entityA->setNicheNumber($newNicheNumber);
        $entityA->setGeoPosition($newGeoPosition);
    }
}
