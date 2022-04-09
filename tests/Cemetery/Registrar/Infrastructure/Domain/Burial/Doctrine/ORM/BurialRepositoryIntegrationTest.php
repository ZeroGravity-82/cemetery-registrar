<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialContainerId;
use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM\BurialRepository as DoctrineOrmBurialRepository;
use Cemetery\Tests\Registrar\Domain\Burial\BurialProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private Burial                      $burialA;
    private Burial                      $burialB;
    private Burial                      $burialC;
    private DoctrineOrmBurialRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->burialA       = BurialProvider::getBurialA();
        $this->burialB       = BurialProvider::getBurialB();
        $this->burialC       = BurialProvider::getBurialC();
        $this->burialD       = BurialProvider::getBurialD();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repo          = new DoctrineOrmBurialRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewBurial(): void
    {
        $this->repo->save($this->burialA);
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B001', $persistedBurial->getId()->getValue());
        $this->assertSame('BC001', $persistedBurial->getCode()->getValue());
        $this->assertSame('D001', $persistedBurial->getDeceasedId()->getValue());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->getCustomerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->getCustomerId()->getId());
        $this->assertSame('NP001', $persistedBurial->getCustomerId()->getId()->getValue());
        $this->assertSame(BurialPlaceType::COLUMBARIUM_NICHE . '.BP001', (string) $persistedBurial->getBurialPlaceId());
        $this->assertNull($persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame(1, $this->getRowCount(Burial::class));
        $this->assertSame(
            $this->burialA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedBurial->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->burialA->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            $persistedBurial->getUpdatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedBurial->getRemovedAt());
    }

    public function testItUpdatesAnExistingBurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->burialA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $persistedBurial->setBuriedAt(null);
        $persistedBurial->setBurialPlaceOwnerId(new NaturalPersonId('NP001'));
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedBurial);
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertNull($persistedBurial->getBuriedAt());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame('NP001', (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame(1, $this->getRowCount(Burial::class));
        $this->assertSame(
            $this->burialA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedBurial->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->burialA->getUpdatedAt() < $persistedBurial->getUpdatedAt());
        $this->assertNull($persistedBurial->getRemovedAt());
    }

    public function testItSavesACollectionOfNewBurials(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->burialA->getId()));
        $this->assertNotNull($this->repo->findById($this->burialB->getId()));
        $this->assertNotNull($this->repo->findById($this->burialC->getId()));
        $this->assertSame(3, $this->getRowCount(Burial::class));
    }

    public function testItUpdatesExistingBurialsWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $persistedBurial->setBuriedAt(null);
        $persistedBurial->setBurialPlaceOwnerId(new NaturalPersonId('NP001'));
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new BurialCollection([$persistedBurial, $this->burialC]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertNull($persistedBurial->getBuriedAt());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame('NP001', (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertTrue($this->burialA->getUpdatedAt() < $persistedBurial->getUpdatedAt());

        $persistedBurial = $this->repo->findById($this->burialB->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B002', (string) $persistedBurial->getId());
        $this->assertSame('BC002', (string) $persistedBurial->getCode());
        $this->assertSame('D002', (string) $persistedBurial->getDeceasedId());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->getCustomerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->getCustomerId()->getId());
        $this->assertSame('NP001', $persistedBurial->getCustomerId()->getId()->getValue());
        $this->assertSame(BurialPlaceType::GRAVE_SITE . '.BP002', (string) $persistedBurial->getBurialPlaceId());
        $this->assertSame('NP001', (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertNull($persistedBurial->getFuneralCompanyId());
        $this->assertSame(BurialContainerType::COFFIN . '.CT001', (string) $persistedBurial->getBurialContainerId());
        $this->assertNull($persistedBurial->getBuriedAt());

        $persistedBurial = $this->repo->findById($this->burialC->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B003', (string) $persistedBurial->getId());
        $this->assertSame('BC003', (string) $persistedBurial->getCode());
        $this->assertSame('D003', (string) $persistedBurial->getDeceasedId());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->getCustomerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->getCustomerId()->getId());
        $this->assertSame('NP001', $persistedBurial->getCustomerId()->getId()->getValue());
        $this->assertInstanceOf(BurialPlaceId::class, $persistedBurial->getBurialPlaceId());
        $this->assertSame(BurialPlaceType::MEMORIAL_TREE . '.BP003', (string) $persistedBurial->getBurialPlaceId());
        $this->assertSame('NP002', (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertInstanceOf(JuristicPersonId::class, $persistedBurial->getFuneralCompanyId()->getId());
        $this->assertSame('JP001', (string) $persistedBurial->getFuneralCompanyId()->getId());
        $this->assertSame(BurialContainerType::COFFIN . '.CT002', (string) $persistedBurial->getBurialContainerId());
        $this->assertNull($persistedBurial->getBuriedAt());

        $this->assertSame(3, $this->getRowCount(Burial::class));
    }

    public function testItHydratesBurialContainerIdEmbeddable(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertNull($persistedBurial->getBurialContainerId());

        $persistedBurial = $this->repo->findById($this->burialB->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertInstanceOf(BurialContainerId::class, $persistedBurial->getBurialContainerId());
        $this->assertSame('CT001', $persistedBurial->getBurialContainerId()->getValue());
        $this->assertSame(BurialContainerType::COFFIN, (string) $persistedBurial->getBurialContainerId()->getType());
    }

    public function testItHydratesBurialPlaceIdEmbeddable(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialD]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertInstanceOf(BurialPlaceId::class, $persistedBurial->getBurialPlaceId());
        $this->assertSame('BP001', $persistedBurial->getBurialPlaceId()->getValue());
        $this->assertSame(BurialPlaceType::COLUMBARIUM_NICHE, (string) $persistedBurial->getBurialPlaceId()->getType());

        $persistedBurial = $this->repo->findById($this->burialD->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertNull($persistedBurial->getBurialPlaceId());
    }

    public function testItRemovesABurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->burialA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->repo->remove($persistedBurial);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->burialA->getId()));
        $this->assertSame(1, $this->getRowCount(Burial::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialA->getId()));
    }

    public function testItRemovesACollectionOfBurials(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurialB = $this->repo->findById($this->burialB->getId());
        $persistedBurialC = $this->repo->findById($this->burialC->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurialB);
        $this->assertInstanceOf(Burial::class, $persistedBurialC);
        $this->repo->removeAll(new BurialCollection([$persistedBurialB, $persistedBurialC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->burialB->getId()));
        $this->assertNull($this->repo->findById($this->burialC->getId()));
        $this->assertNotNull($this->repo->findById($this->burialA->getId()));
        $this->assertSame(3, $this->getRowCount(Burial::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialB->getId()));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialC->getId()));
        $this->assertNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialA->getId()));
    }

    public function testItFindsABurialById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialB->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B002', (string) $persistedBurial->getId());
    }

    public function testItReturnsNullIfABurialIsNotFoundById(): void
    {
        $burial = $this->repo->findById(new BurialId('unknown_id'));
        $this->assertNull($burial);
    }

    public function testItCountsBurialsByFuneralCompanyId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC, $this->burialD]));
        $this->entityManager->clear();
        $this->assertSame(4, $this->getRowCount(Burial::class));

        // Testing itself
        $knownFuneralCompanyId = new FuneralCompanyId(new JuristicPersonId('JP001'));
        $burialCount           = $this->repo->countByFuneralCompanyId($knownFuneralCompanyId);
        $this->assertSame(2, $burialCount);

        $unknownFuneralCompanyId = new FuneralCompanyId(new SoleProprietorId('unknown_id'));
        $burialCount             = $this->repo->countByFuneralCompanyId($unknownFuneralCompanyId);
        $this->assertSame(0, $burialCount);
    }
}
