<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM\ColumbariumNicheRepository as DoctrineOrmColumbariumNicheRepository;
use Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private ColumbariumNiche                      $columbariumNicheA;
    private ColumbariumNiche                      $columbariumNicheB;
    private ColumbariumNiche                      $columbariumNicheC;
    private DoctrineOrmColumbariumNicheRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->columbariumNicheA = ColumbariumNicheProvider::getColumbariumNicheA();
        $this->columbariumNicheB = ColumbariumNicheProvider::getColumbariumNicheB();
        $this->columbariumNicheC = ColumbariumNicheProvider::getColumbariumNicheC();
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineOrmColumbariumNicheRepository($this->entityManager);
        $this->truncateEntities();
    }

    public function testItSavesANewColumbariumNiche(): void
    {
        $this->repo->save($this->columbariumNicheA);
        $this->entityManager->clear();

        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheA->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $this->assertInstanceOf(ColumbariumNicheId::class, $persistedColumbariumNiche->id());
        $this->assertTrue($persistedColumbariumNiche->id()->isEqual($this->columbariumNicheA->id()));
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbariumNiche->columbariumId());
        $this->assertTrue($persistedColumbariumNiche->columbariumId()->isEqual($this->columbariumNicheA->columbariumId()));
        $this->assertInstanceOf(RowInColumbarium::class, $persistedColumbariumNiche->rowInColumbarium());
        $this->assertTrue($persistedColumbariumNiche->rowInColumbarium()->isEqual($this->columbariumNicheA->rowInColumbarium()));
        $this->assertInstanceOf(ColumbariumNicheNumber::class, $persistedColumbariumNiche->nicheNumber());
        $this->assertTrue($persistedColumbariumNiche->nicheNumber()->isEqual($this->columbariumNicheA->nicheNumber()));
        $this->assertNull($persistedColumbariumNiche->geoPosition());
        $this->assertSame(1, $this->getRowCount(ColumbariumNiche::class));
        $this->assertSame(
            $this->columbariumNicheA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedColumbariumNiche->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->columbariumNicheA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedColumbariumNiche->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedColumbariumNiche->removedAt());
    }

    public function testItUpdatesAnExistingColumbariumNiche(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->columbariumNicheA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(ColumbariumNiche::class));

        // Testing itself
        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheA->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $newColumbariumId    = new ColumbariumId('C020');
        $newRowInColumbarium = new RowInColumbarium(20);
        $newNicheNumber      = new ColumbariumNicheNumber('020');
        $newGeoPosition      = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);
        $persistedColumbariumNiche->setColumbariumId($newColumbariumId);
        $persistedColumbariumNiche->setRowInColumbarium($newRowInColumbarium);
        $persistedColumbariumNiche->setNicheNumber($newNicheNumber);
        $persistedColumbariumNiche->setGeoPosition($newGeoPosition);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedColumbariumNiche);
        $this->entityManager->clear();

        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheA->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbariumNiche->columbariumId());
        $this->assertTrue($persistedColumbariumNiche->columbariumId()->isEqual($newColumbariumId));
        $this->assertInstanceOf(RowInColumbarium::class, $persistedColumbariumNiche->rowInColumbarium());
        $this->assertTrue($persistedColumbariumNiche->rowInColumbarium()->isEqual($newRowInColumbarium));
        $this->assertInstanceOf(ColumbariumNicheNumber::class, $persistedColumbariumNiche->nicheNumber());
        $this->assertTrue($persistedColumbariumNiche->nicheNumber()->isEqual($newNicheNumber));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbariumNiche->geoPosition());
        $this->assertTrue($persistedColumbariumNiche->geoPosition()->isEqual($newGeoPosition));
        $this->assertSame(1, $this->getRowCount(ColumbariumNiche::class));
        $this->assertSame(
            $this->columbariumNicheA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedColumbariumNiche->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->columbariumNicheA->updatedAt() < $persistedColumbariumNiche->updatedAt());
        $this->assertNull($persistedColumbariumNiche->removedAt());
    }

    public function testItSavesACollectionOfNewColumbariumNiches(): void
    {
        $this->repo->saveAll(new ColumbariumNicheCollection(
            [$this->columbariumNicheA, $this->columbariumNicheB, $this->columbariumNicheC]
        ));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->columbariumNicheA->id()));
        $this->assertNotNull($this->repo->findById($this->columbariumNicheB->id()));
        $this->assertNotNull($this->repo->findById($this->columbariumNicheC->id()));
        $this->assertSame(3, $this->getRowCount(ColumbariumNiche::class));
    }

    public function testItUpdatesExistingColumbariumNicheWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection([$this->columbariumNicheA, $this->columbariumNicheB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(ColumbariumNiche::class));

        // Testing itself
        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheA->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $newColumbariumId    = new ColumbariumId('C020');
        $newRowInColumbarium = new RowInColumbarium(20);
        $newNicheNumber      = new ColumbariumNicheNumber('020');
        $newGeoPosition      = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);
        $persistedColumbariumNiche->setColumbariumId($newColumbariumId);
        $persistedColumbariumNiche->setRowInColumbarium($newRowInColumbarium);
        $persistedColumbariumNiche->setNicheNumber($newNicheNumber);
        $persistedColumbariumNiche->setGeoPosition($newGeoPosition);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new ColumbariumNicheCollection([$persistedColumbariumNiche, $this->columbariumNicheC]));
        $this->entityManager->clear();

        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheA->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbariumNiche->columbariumId());
        $this->assertTrue($persistedColumbariumNiche->columbariumId()->isEqual($newColumbariumId));
        $this->assertInstanceOf(RowInColumbarium::class, $persistedColumbariumNiche->rowInColumbarium());
        $this->assertTrue($persistedColumbariumNiche->rowInColumbarium()->isEqual($newRowInColumbarium));
        $this->assertInstanceOf(ColumbariumNicheNumber::class, $persistedColumbariumNiche->nicheNumber());
        $this->assertTrue($persistedColumbariumNiche->nicheNumber()->isEqual($newNicheNumber));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbariumNiche->geoPosition());
        $this->assertTrue($persistedColumbariumNiche->geoPosition()->isEqual($newGeoPosition));
        $this->assertTrue($this->columbariumNicheA->updatedAt() < $persistedColumbariumNiche->updatedAt());

        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheB->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $this->assertInstanceOf(ColumbariumNicheId::class, $persistedColumbariumNiche->id());
        $this->assertTrue($persistedColumbariumNiche->id()->isEqual($this->columbariumNicheB->id()));
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbariumNiche->columbariumId());
        $this->assertTrue($persistedColumbariumNiche->columbariumId()->isEqual($this->columbariumNicheB->columbariumId()));
        $this->assertInstanceOf(RowInColumbarium::class, $persistedColumbariumNiche->rowInColumbarium());
        $this->assertTrue($persistedColumbariumNiche->rowInColumbarium()->isEqual($this->columbariumNicheB->rowInColumbarium()));
        $this->assertInstanceOf(ColumbariumNicheNumber::class, $persistedColumbariumNiche->nicheNumber());
        $this->assertTrue($persistedColumbariumNiche->nicheNumber()->isEqual($this->columbariumNicheB->nicheNumber()));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbariumNiche->geoPosition());
        $this->assertTrue($persistedColumbariumNiche->geoPosition()->isEqual($this->columbariumNicheB->geoPosition()));

        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheC->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $this->assertInstanceOf(ColumbariumNicheId::class, $persistedColumbariumNiche->id());
        $this->assertTrue($persistedColumbariumNiche->id()->isEqual($this->columbariumNicheC->id()));
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbariumNiche->columbariumId());
        $this->assertTrue($persistedColumbariumNiche->columbariumId()->isEqual($this->columbariumNicheC->columbariumId()));
        $this->assertInstanceOf(RowInColumbarium::class, $persistedColumbariumNiche->rowInColumbarium());
        $this->assertTrue($persistedColumbariumNiche->rowInColumbarium()->isEqual($this->columbariumNicheC->rowInColumbarium()));
        $this->assertInstanceOf(ColumbariumNicheNumber::class, $persistedColumbariumNiche->nicheNumber());
        $this->assertTrue($persistedColumbariumNiche->nicheNumber()->isEqual($this->columbariumNicheC->nicheNumber()));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbariumNiche->geoPosition());
        $this->assertTrue($persistedColumbariumNiche->geoPosition()->isEqual($this->columbariumNicheC->geoPosition()));

        $this->assertSame(3, $this->getRowCount(ColumbariumNiche::class));
    }

    public function testItRemovesAColumbariumNiche(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->columbariumNicheA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(ColumbariumNiche::class));

        // Testing itself
        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheA->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $this->repo->remove($persistedColumbariumNiche);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->columbariumNicheA->id()));
        $this->assertSame(1, $this->getRowCount(ColumbariumNiche::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(ColumbariumNiche::class, $this->columbariumNicheA->id()->value()));
    }

    public function testItRemovesACollectionOfColumbariumNiches(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection(
            [$this->columbariumNicheA, $this->columbariumNicheB, $this->columbariumNicheC]
        ));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(ColumbariumNiche::class));

        // Testing itself
        $persistedColumbariumNicheB = $this->repo->findById($this->columbariumNicheB->id());
        $persistedColumbariumNicheC = $this->repo->findById($this->columbariumNicheC->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNicheB);
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNicheC);
        $this->repo->removeAll(new ColumbariumNicheCollection([$persistedColumbariumNicheB, $persistedColumbariumNicheC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->columbariumNicheB->id()));
        $this->assertNull($this->repo->findById($this->columbariumNicheC->id()));
        $this->assertNotNull($this->repo->findById($this->columbariumNicheA->id()));
        $this->assertSame(3, $this->getRowCount(ColumbariumNiche::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(ColumbariumNiche::class, $this->columbariumNicheB->id()->value()));
        $this->assertNotNull($this->getRemovedAtTimestampById(ColumbariumNiche::class, $this->columbariumNicheC->id()->value()));
        $this->assertNull($this->getRemovedAtTimestampById(ColumbariumNiche::class, $this->columbariumNicheA->id()->value()));
    }

    public function testItFindsAColumbariumNicheById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumNicheCollection(
            [$this->columbariumNicheA, $this->columbariumNicheB, $this->columbariumNicheC]
        ));
        $this->entityManager->clear();

        // Testing itself
        $persistedColumbariumNiche = $this->repo->findById($this->columbariumNicheB->id());
        $this->assertInstanceOf(ColumbariumNiche::class, $persistedColumbariumNiche);
        $this->assertTrue($persistedColumbariumNiche->id()->isEqual($this->columbariumNicheB->id()));
    }

    public function testItReturnsNullIfAColumbariumNicheIsNotFoundById(): void
    {
        $columbariumNiche = $this->repo->findById(new ColumbariumNicheId('unknown_id'));
        $this->assertNull($columbariumNiche);
    }
}
