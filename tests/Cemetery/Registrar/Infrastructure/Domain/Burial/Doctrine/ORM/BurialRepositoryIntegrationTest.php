<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\CustomerType;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Site\SiteId;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM\DoctrineORMBurialRepository;
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

    private DoctrineORMBurialRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->buildBurials();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repo          = new DoctrineORMBurialRepository(
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
        $this->assertSame((string) $this->burialA->getDeceasedId(), (string) $persistedBurial->getDeceasedId());
        $this->assertSame((string) $this->burialA->getCustomerId(), (string) $persistedBurial->getCustomerId());
        $this->assertSame((string) $this->burialA->getSiteId(), (string) $persistedBurial->getSiteId());
        $this->assertSame((string) $this->burialA->getSiteOwnerId(), (string) $persistedBurial->getSiteOwnerId());
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

    public function testItHydratesCustomerIdEmbeddable(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialC]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->getCustomerId());
        $persistedBurial = $this->repo->findById($this->burialC->getId());
        $this->assertNull($persistedBurial->getCustomerId());
    }

    public function testItRemovesABurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->burialA);
        $this->assertSame(1, $this->getRowCount());
        $this->entityManager->clear();

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->getId());
        $this->repo->remove($persistedBurial);
        $this->assertSame(0, $this->getRowCount());
    }

    public function testItRemovesACollectionOfBurials(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->assertSame(3, $this->getRowCount());
        $this->entityManager->clear();

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

    private function buildBurials(): void
    {
        $idA              = new BurialId('B001');
        $idB              = new BurialId('B002');
        $idC              = new BurialId('B003');
        $burialCodeA      = new BurialCode('BC001');
        $burialCodeB      = new BurialCode('BC002');
        $burialCodeC      = new BurialCode('BC003');
        $naturalPersonIdA = new NaturalPersonId('NP001');
        $naturalPersonIdB = new NaturalPersonId('NP002');
        $naturalPersonIdC = new NaturalPersonId('NP003');
        $customerId       = new CustomerId('C001', CustomerType::naturalPerson());
        $siteIdA          = new SiteId('S001');
        $siteIdB          = new SiteId('S002');
        $siteIdC          = new SiteId('S003');
        $this->burialA    = new Burial($idA, $burialCodeA, $naturalPersonIdA, $siteIdA, $customerId, null);
        $this->burialB    = new Burial($idB, $burialCodeB, $naturalPersonIdB, $siteIdB, $customerId, $naturalPersonIdB);
        $this->burialC    = new Burial($idC, $burialCodeC, $naturalPersonIdC, $siteIdC, null, $naturalPersonIdC);
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
