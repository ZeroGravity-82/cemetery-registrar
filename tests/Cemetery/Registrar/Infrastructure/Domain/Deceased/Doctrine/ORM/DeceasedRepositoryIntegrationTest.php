<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Deceased\Doctrine\ORM;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Domain\Deceased\Doctrine\ORM\DeceasedRepository as DoctrineOrmDeceasedRepository;
use Cemetery\Tests\Registrar\Domain\Deceased\DeceasedProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedRepositoryIntegrationTest extends KernelTestCase
{
    private Deceased $deceasedA;

    private Deceased $deceasedB;

    private Deceased $deceasedC;

    private EntityManagerInterface $entityManager;

    private DoctrineOrmDeceasedRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->deceasedA     = DeceasedProvider::getDeceasedA();
        $this->deceasedB     = DeceasedProvider::getDeceasedB();
        $this->deceasedC     = DeceasedProvider::getDeceasedC();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->repo          = new DoctrineOrmDeceasedRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewDeceased(): void
    {
        $this->repo->save($this->deceasedA);
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertSame((string) $this->deceasedA->getId(), (string) $persistedDeceased->getId());
        $this->assertSame((string) $this->deceasedA->getNaturalPersonId(), (string) $persistedDeceased->getNaturalPersonId());
        $this->assertSame($this->deceasedA->getDiedAt()->format('Y-m-d'), $persistedDeceased->getDiedAt()->format('Y-m-d'));
        $this->assertNull($persistedDeceased->getDeathCertificateId());
        $this->assertNull($persistedDeceased->getCauseOfDeath());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItUpdatesAnExistingDeceased(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->deceasedA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedDeceased  = $this->repo->findById($this->deceasedA->getId());
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Some cause 1');
        $persistedDeceased->setDeathCertificateId($deathCertificateId);
        $persistedDeceased->setCauseOfDeath($causeOfDeath);
        $this->repo->save($persistedDeceased);
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->getCauseOfDeath());
        $this->assertSame('Some cause 1', (string) $persistedDeceased->getCauseOfDeath());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItSavesACollectionOfNewDeceaseds(): void
    {
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->deceasedA->getId()));
        $this->assertNotNull($this->repo->findById($this->deceasedB->getId()));
        $this->assertNotNull($this->repo->findById($this->deceasedC->getId()));
        $this->assertSame(3, $this->getRowCount());
    }

    public function testItUpdatesExistingDeceasedWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount());

        // Testing itself
        $persistedDeceased  = $this->repo->findById($this->deceasedA->getId());
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Some cause 1');
        $persistedDeceased->setDeathCertificateId($deathCertificateId);
        $persistedDeceased->setCauseOfDeath($causeOfDeath);
        $this->repo->saveAll(new DeceasedCollection([$persistedDeceased, $this->deceasedC]));
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->getCauseOfDeath());
        $this->assertSame('Some cause 1', (string) $persistedDeceased->getCauseOfDeath());

        $persistedDeceased = $this->repo->findById($this->deceasedB->getId());
        $this->assertInstanceOf(DeceasedId::class, $persistedDeceased->getId());
        $this->assertSame('D002', (string) $persistedDeceased->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedDeceased->getNaturalPersonId());
        $this->assertSame('NP002', (string) $persistedDeceased->getNaturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedDeceased->getDiedAt());
        $this->assertSame('2001-02-11', $persistedDeceased->getDiedAt()->format('Y-m-d'));
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->getCauseOfDeath());
        $this->assertSame('Some cause 1', (string) $persistedDeceased->getCauseOfDeath());

        $persistedDeceased = $this->repo->findById($this->deceasedC->getId());
        $this->assertInstanceOf(DeceasedId::class, $persistedDeceased->getId());
        $this->assertSame('D003', (string) $persistedDeceased->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedDeceased->getNaturalPersonId());
        $this->assertSame('NP003', (string) $persistedDeceased->getNaturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedDeceased->getDiedAt());
        $this->assertSame('2011-05-13', $persistedDeceased->getDiedAt()->format('Y-m-d'));
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC002', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertNull($persistedDeceased->getCauseOfDeath());

        $this->assertSame(3, $this->getRowCount());
    }

    public function testItRemovesADeceased(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->deceasedA);
        $this->assertSame(1, $this->getRowCount());
        $this->entityManager->clear();

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->repo->remove($persistedDeceased);
        $this->assertSame(0, $this->getRowCount());
    }

    public function testItRemovesACollectionOfDeceaseds(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->assertSame(3, $this->getRowCount());
        $this->entityManager->clear();

        // Testing itself
        $persistedDeceasedB = $this->repo->findById($this->deceasedB->getId());
        $persistedDeceasedC = $this->repo->findById($this->deceasedC->getId());
        $this->repo->removeAll(new DeceasedCollection([$persistedDeceasedB, $persistedDeceasedC]));
        $this->assertSame(1, $this->getRowCount());
        $this->assertNotNull($this->repo->findById($this->deceasedA->getId()));
    }

    public function testItFindsADeceasedById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedB->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertSame((string) $this->deceasedB->getId(), (string) $persistedDeceased->getId());
    }

    public function testItReturnsNullIfADeceasedIsNotFoundById(): void
    {
        $deceased = $this->repo->findById(new DeceasedId('unknown_id'));

        $this->assertNull($deceased);
    }

    private function truncateEntities(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    private function getRowCount(): int
    {
        return (int) $this->entityManager
            ->getRepository(Deceased::class)
            ->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
