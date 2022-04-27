<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\ColumbariumNiche\Doctrine\ORM\ColumbariumRepository as DoctrineOrmColumbariumRepository;
use Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private Columbarium                      $columbariumA;
    private Columbarium                      $columbariumB;
    private Columbarium                      $columbariumC;
    private DoctrineOrmColumbariumRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->columbariumA = ColumbariumProvider::getColumbariumA();
        $this->columbariumB = ColumbariumProvider::getColumbariumB();
        $this->columbariumC = ColumbariumProvider::getColumbariumC();
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineOrmColumbariumRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewColumbarium(): void
    {
        $this->repo->save($this->columbariumA);
        $this->entityManager->clear();

        $persistedColumbarium = $this->repo->findById($this->columbariumA->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbarium->id());
        $this->assertTrue($persistedColumbarium->id()->isEqual($this->columbariumA->id()));
        $this->assertInstanceOf(ColumbariumName::class, $persistedColumbarium->name());
        $this->assertTrue($persistedColumbarium->name()->isEqual($this->columbariumA->name()));
        $this->assertNull($persistedColumbarium->geoPosition());
        $this->assertSame(1, $this->getRowCount(Columbarium::class));
        $this->assertSame(
            $this->columbariumA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedColumbarium->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->columbariumA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedColumbarium->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedColumbarium->removedAt());
    }

    public function testItUpdatesAnExistingColumbarium(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->columbariumA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Columbarium::class));

        // Testing itself
        $persistedColumbarium = $this->repo->findById($this->columbariumA->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $newName        = new ColumbariumName('западный колумбарий 2');
        $newGeoPosition = new GeoPosition(new Coordinates('50.950357', '80.7972252'), null);
        $persistedColumbarium->setName($newName);
        $persistedColumbarium->setGeoPosition($newGeoPosition);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedColumbarium);
        $this->entityManager->clear();

        $persistedColumbarium = $this->repo->findById($this->columbariumA->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->assertInstanceOf(ColumbariumName::class, $persistedColumbarium->name());
        $this->assertTrue($persistedColumbarium->name()->isEqual($newName));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbarium->geoPosition());
        $this->assertTrue($persistedColumbarium->geoPosition()->isEqual($newGeoPosition));
        $this->assertSame(1, $this->getRowCount(Columbarium::class));
        $this->assertSame(
            $this->columbariumA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedColumbarium->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->columbariumA->updatedAt() < $persistedColumbarium->updatedAt());
        $this->assertNull($persistedColumbarium->removedAt());
    }

    public function testItSavesACollectionOfNewColumbariums(): void
    {
        $this->repo->saveAll(new ColumbariumCollection([$this->columbariumA, $this->columbariumB, $this->columbariumC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->columbariumA->id()));
        $this->assertNotNull($this->repo->findById($this->columbariumB->id()));
        $this->assertNotNull($this->repo->findById($this->columbariumC->id()));
        $this->assertSame(3, $this->getRowCount(Columbarium::class));
    }

    public function testItUpdatesExistingColumbariumWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumCollection([$this->columbariumA, $this->columbariumB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(Columbarium::class));

        // Testing itself
        $persistedColumbarium = $this->repo->findById($this->columbariumA->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $newName        = new ColumbariumName('западный колумбарий 2');
        $newGeoPosition = new GeoPosition(new Coordinates('-50.950357', '-170.7972252'), null);
        $persistedColumbarium->setName($newName);
        $persistedColumbarium->setGeoPosition($newGeoPosition);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new ColumbariumCollection([$persistedColumbarium, $this->columbariumC]));
        $this->entityManager->clear();

        $persistedColumbarium = $this->repo->findById($this->columbariumA->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->assertInstanceOf(ColumbariumName::class, $persistedColumbarium->name());
        $this->assertTrue($persistedColumbarium->name()->isEqual($newName));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbarium->geoPosition());
        $this->assertTrue($persistedColumbarium->geoPosition()->isEqual($newGeoPosition));
        $this->assertTrue($this->columbariumA->updatedAt() < $persistedColumbarium->updatedAt());

        $persistedColumbarium = $this->repo->findById($this->columbariumB->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbarium->id());
        $this->assertTrue($persistedColumbarium->id()->isEqual($this->columbariumB->id()));
        $this->assertInstanceOf(ColumbariumName::class, $persistedColumbarium->name());
        $this->assertTrue($persistedColumbarium->name()->isEqual($this->columbariumB->name()));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbarium->geoPosition());
        $this->assertTrue($persistedColumbarium->geoPosition()->isEqual($this->columbariumB->geoPosition()));

        $persistedColumbarium = $this->repo->findById($this->columbariumC->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->assertInstanceOf(ColumbariumId::class, $persistedColumbarium->id());
        $this->assertTrue($persistedColumbarium->id()->isEqual($this->columbariumC->id()));
        $this->assertInstanceOf(ColumbariumName::class, $persistedColumbarium->name());
        $this->assertTrue($persistedColumbarium->name()->isEqual($this->columbariumC->name()));
        $this->assertInstanceOf(GeoPosition::class, $persistedColumbarium->geoPosition());
        $this->assertTrue($persistedColumbarium->geoPosition()->isEqual($this->columbariumC->geoPosition()));

        $this->assertSame(3, $this->getRowCount(Columbarium::class));
    }

    public function testItRemovesAColumbarium(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->columbariumA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Columbarium::class));

        // Testing itself
        $persistedColumbarium = $this->repo->findById($this->columbariumA->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->repo->remove($persistedColumbarium);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->columbariumA->id()));
        $this->assertSame(1, $this->getRowCount(Columbarium::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Columbarium::class, (string) $this->columbariumA->id()));
    }

    public function testItHydratesGeoPositionEmbeddable(): void
    {
        $this->repo->saveAll(new ColumbariumCollection([$this->columbariumA, $this->columbariumB]));
        $this->entityManager->clear();

        $persistedColumbarium = $this->repo->findById($this->columbariumA->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->assertNull($persistedColumbarium->geoPosition());

//        $persistedColumbarium = $this->repo->findById($this->columbariumB->id());
//        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
//        $this->assertInstanceOf(GeoPosition::class, $persistedColumbarium->geoPosition());
//        $this->assertTrue($persistedColumbarium->geoPosition()->isEqual($this->columbariumB->geoPosition()));
    }

    public function testItRemovesACollectionOfColumbariums(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumCollection([$this->columbariumA, $this->columbariumB, $this->columbariumC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(Columbarium::class));

        // Testing itself
        $persistedColumbariumB = $this->repo->findById($this->columbariumB->id());
        $persistedColumbariumC = $this->repo->findById($this->columbariumC->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbariumB);
        $this->assertInstanceOf(Columbarium::class, $persistedColumbariumC);
        $this->repo->removeAll(new ColumbariumCollection([$persistedColumbariumB, $persistedColumbariumC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->columbariumB->id()));
        $this->assertNull($this->repo->findById($this->columbariumC->id()));
        $this->assertNotNull($this->repo->findById($this->columbariumA->id()));
        $this->assertSame(3, $this->getRowCount(Columbarium::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Columbarium::class, $this->columbariumB->id()->value()));
        $this->assertNotNull($this->getRemovedAtTimestampById(Columbarium::class, $this->columbariumC->id()->value()));
        $this->assertNull($this->getRemovedAtTimestampById(Columbarium::class, $this->columbariumA->id()->value()));
    }

    public function testItFindsAColumbariumById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new ColumbariumCollection([$this->columbariumA, $this->columbariumB, $this->columbariumC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedColumbarium = $this->repo->findById($this->columbariumB->id());
        $this->assertInstanceOf(Columbarium::class, $persistedColumbarium);
        $this->assertTrue($persistedColumbarium->id()->isEqual($this->columbariumB->id()));
    }

    public function testItReturnsNullIfAColumbariumIsNotFoundById(): void
    {
        $columbarium = $this->repo->findById(new ColumbariumId('unknown_id'));
        $this->assertNull($columbarium);
    }
}
