<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM\BurialRepository as DoctrineOrmBurialRepository;
use Cemetery\Tests\Registrar\Domain\Burial\BurialProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialRepositoryIntegrationTest extends KernelTestCase
{
    private Burial $burialA;

    private Burial $burialB;

    private Burial $burialC;

    private EntityManagerInterface $entityManager;

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
        $this->assertSame((string) $this->burialA->getId(), (string) $persistedBurial->getId());
        $this->assertSame((string) $this->burialA->getCode(), (string) $persistedBurial->getCode());
        $this->assertSame((string) $this->burialA->getDeceasedId(), (string) $persistedBurial->getDeceasedId());
        $this->assertSame((string) $this->burialA->getCustomerId(), (string) $persistedBurial->getCustomerId());
        $this->assertSame((string) $this->burialA->getBurialPlaceId(), (string) $persistedBurial->getBurialPlaceId());
        $this->assertSame((string) $this->burialA->getBurialPlaceOwnerId(), (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItUpdatesAnExistingBurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->burialA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $persistedBurial->setBuriedAt(null);
        $persistedBurial->setBurialPlaceOwnerId(new NaturalPersonId('NP001'));
        $this->repo->save($persistedBurial);
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertNull($persistedBurial->getBuriedAt());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame('NP001', (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItSavesACollectionOfNewBurials(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->burialA->getId()));
        $this->assertNotNull($this->repo->findById($this->burialB->getId()));
        $this->assertNotNull($this->repo->findById($this->burialC->getId()));
        $this->assertSame(3, $this->getRowCount());
    }

    public function testItUpdatesExistingBurialsWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount());

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $persistedBurial->setBuriedAt(null);
        $persistedBurial->setBurialPlaceOwnerId(new NaturalPersonId('NP001'));
        $this->repo->saveAll(new BurialCollection([$persistedBurial, $this->burialC]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertNull($persistedBurial->getBuriedAt());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame('NP001', (string) $persistedBurial->getBurialPlaceOwnerId());

        $persistedBurial = $this->repo->findById($this->burialB->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame((string) $this->burialB->getId(), (string) $persistedBurial->getId());
        $this->assertSame((string) $this->burialB->getCode(), (string) $persistedBurial->getCode());
        $this->assertSame((string) $this->burialB->getDeceasedId(), (string) $persistedBurial->getDeceasedId());
        $this->assertSame((string) $this->burialB->getCustomerId(), (string) $persistedBurial->getCustomerId());
        $this->assertSame((string) $this->burialB->getBurialPlaceId(), (string) $persistedBurial->getBurialPlaceId());
        $this->assertSame((string) $this->burialB->getBurialPlaceOwnerId(), (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertNull($persistedBurial->getFuneralCompanyId());
        $this->assertSame((string) $this->burialB->getBurialContainerId(), (string) $persistedBurial->getBurialContainerId());
        $this->assertNull($persistedBurial->getBuriedAt());

        $persistedBurial = $this->repo->findById($this->burialC->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame((string) $this->burialC->getId(), (string) $persistedBurial->getId());
        $this->assertSame((string) $this->burialC->getCode(), (string) $persistedBurial->getCode());
        $this->assertSame((string) $this->burialC->getDeceasedId(), (string) $persistedBurial->getDeceasedId());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->getCustomerId());
        $this->assertSame(CustomerType::NATURAL_PERSON . '.' . 'C001', (string) $persistedBurial->getCustomerId());
        $this->assertInstanceOf(BurialPlaceId::class, $persistedBurial->getBurialPlaceId());
        $this->assertSame(BurialPlaceType::MEMORIAL_TREE . '.' . 'BP003', (string) $persistedBurial->getBurialPlaceId());
        $this->assertSame((string) $this->burialC->getBurialPlaceOwnerId(), (string) $persistedBurial->getBurialPlaceOwnerId());
        $this->assertSame((string) $this->burialC->getFuneralCompanyId(), (string) $persistedBurial->getFuneralCompanyId());
        $this->assertSame((string) $this->burialC->getBurialContainerId(), (string) $persistedBurial->getBurialContainerId());
        $this->assertNull($persistedBurial->getBuriedAt());

        $this->assertSame(3, $this->getRowCount());
    }

    public function testItHydratesBurialPlaceIdEmbeddable(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialD]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(BurialPlaceId::class, $persistedBurial->getBurialPlaceId());
        $this->assertSame(BurialPlaceType::COLUMBARIUM_NICHE . '.' . 'BP001', (string) $persistedBurial->getBurialPlaceId());
        $persistedBurial = $this->repo->findById($this->burialD->getId());
        $this->assertNull($persistedBurial->getBurialPlaceId());
    }

    public function testItHydratesCustomerIdEmbeddable(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialD]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->getCustomerId());
        $this->assertSame(CustomerType::NATURAL_PERSON . '.' . 'C001', (string) $persistedBurial->getCustomerId());
        $persistedBurial = $this->repo->findById($this->burialD->getId());
        $this->assertNull($persistedBurial->getCustomerId());
    }

    public function testItHydratesFuneralCompanyIdEmbeddable(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialB, $this->burialC]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialB->getId());
        $this->assertNull($persistedBurial->getFuneralCompanyId());
        $persistedBurial = $this->repo->findById($this->burialC->getId());
        $this->assertInstanceOf(FuneralCompanyId::class, $persistedBurial->getFuneralCompanyId());
        $this->assertSame(FuneralCompanyType::SOLE_PROPRIETOR . '.' . 'FC001', (string) $persistedBurial->getFuneralCompanyId());
    }

    public function testItRemovesABurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->burialA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->repo->remove($persistedBurial);
        $this->assertSame(0, $this->getRowCount());
    }

    public function testItRemovesACollectionOfBurials(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount());

        // Testing itself
        $persistedBurialB = $this->repo->findById($this->burialB->getId());
        $persistedBurialC = $this->repo->findById($this->burialC->getId());
        $this->repo->removeAll(new BurialCollection([$persistedBurialB, $persistedBurialC]));
        $this->assertSame(1, $this->getRowCount());
        $this->assertNotNull($this->repo->findById($this->burialA->getId()));
    }

    public function testItFindsABurialById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialB->getId());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame((string) $this->burialB->getId(), (string) $persistedBurial->getId());
    }

    public function testItReturnsNullIfABurialIsNotFoundById(): void
    {
        $burial = $this->repo->findById(new BurialId('unknown_id'));

        $this->assertNull($burial);
    }

    private function truncateEntities(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    private function getRowCount(): int
    {
        return (int) $this->entityManager
            ->getRepository(Burial::class)
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
