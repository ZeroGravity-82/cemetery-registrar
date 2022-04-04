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
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private Deceased                      $deceasedA;
    private Deceased                      $deceasedB;
    private Deceased                      $deceasedC;
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
        $this->assertSame('D001', (string) $persistedDeceased->getId());
        $this->assertSame('NP001', (string) $persistedDeceased->getNaturalPersonId());
        $this->assertSame(
            $this->deceasedA->getDiedAt()->format('Y-m-d'),
            $persistedDeceased->getDiedAt()->format('Y-m-d')
        );
        $this->assertNull($persistedDeceased->getDeathCertificateId());
        $this->assertNull($persistedDeceased->getCauseOfDeath());
        $this->assertSame(1, $this->getRowCount(Deceased::class));
        $this->assertSame(
            $this->deceasedA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedDeceased->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->deceasedA->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            $persistedDeceased->getUpdatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedDeceased->getRemovedAt());
    }

    public function testItUpdatesAnExistingDeceased(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->deceasedA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Некоторая причина смерти 1');
        $persistedDeceased->setDeathCertificateId($deathCertificateId);
        $persistedDeceased->setCauseOfDeath($causeOfDeath);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedDeceased);
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->getCauseOfDeath());
        $this->assertSame('Некоторая причина смерти 1', (string) $persistedDeceased->getCauseOfDeath());
        $this->assertSame(1, $this->getRowCount(Deceased::class));
        $this->assertSame(
            $this->deceasedA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedDeceased->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->deceasedA->getUpdatedAt() < $persistedDeceased->getUpdatedAt());
        $this->assertNull($persistedDeceased->getRemovedAt());
    }

    public function testItSavesACollectionOfNewDeceaseds(): void
    {
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->deceasedA->getId()));
        $this->assertNotNull($this->repo->findById($this->deceasedB->getId()));
        $this->assertNotNull($this->repo->findById($this->deceasedC->getId()));
        $this->assertSame(3, $this->getRowCount(Deceased::class));
    }

    public function testItUpdatesExistingDeceasedWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Некоторая причина смерти 1');
        $persistedDeceased->setDeathCertificateId($deathCertificateId);
        $persistedDeceased->setCauseOfDeath($causeOfDeath);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new DeceasedCollection([$persistedDeceased, $this->deceasedC]));
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->getCauseOfDeath());
        $this->assertSame('Некоторая причина смерти 1', (string) $persistedDeceased->getCauseOfDeath());
        $this->assertTrue($this->deceasedA->getUpdatedAt() < $persistedDeceased->getUpdatedAt());

        $persistedDeceased = $this->repo->findById($this->deceasedB->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeceasedId::class, $persistedDeceased->getId());
        $this->assertSame('D002', (string) $persistedDeceased->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedDeceased->getNaturalPersonId());
        $this->assertSame('NP002', (string) $persistedDeceased->getNaturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedDeceased->getDiedAt());
        $this->assertSame('2001-02-11', $persistedDeceased->getDiedAt()->format('Y-m-d'));
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->getCauseOfDeath());
        $this->assertSame('Некоторая причина смерти 1', (string) $persistedDeceased->getCauseOfDeath());

        $persistedDeceased = $this->repo->findById($this->deceasedC->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeceasedId::class, $persistedDeceased->getId());
        $this->assertSame('D003', (string) $persistedDeceased->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedDeceased->getNaturalPersonId());
        $this->assertSame('NP003', (string) $persistedDeceased->getNaturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedDeceased->getDiedAt());
        $this->assertSame('2011-05-13', $persistedDeceased->getDiedAt()->format('Y-m-d'));
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->getDeathCertificateId());
        $this->assertSame('DC002', (string) $persistedDeceased->getDeathCertificateId());
        $this->assertNull($persistedDeceased->getCauseOfDeath());

        $this->assertSame(3, $this->getRowCount(Deceased::class));
    }

    public function testItRemovesADeceased(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->deceasedA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedA->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->repo->remove($persistedDeceased);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->deceasedA->getId()));
        $this->assertSame(1, $this->getRowCount(Deceased::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedA->getId()));
    }

    public function testItRemovesACollectionOfDeceaseds(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceasedB = $this->repo->findById($this->deceasedB->getId());
        $persistedDeceasedC = $this->repo->findById($this->deceasedC->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceasedB);
        $this->assertInstanceOf(Deceased::class, $persistedDeceasedC);
        $this->repo->removeAll(new DeceasedCollection([$persistedDeceasedB, $persistedDeceasedC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->deceasedB->getId()));
        $this->assertNull($this->repo->findById($this->deceasedC->getId()));
        $this->assertNotNull($this->repo->findById($this->deceasedA->getId()));
        $this->assertSame(3, $this->getRowCount(Deceased::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedB->getId()));
        $this->assertNotNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedC->getId()));
        $this->assertNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedA->getId()));
    }

    public function testItFindsADeceasedById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedB->getId());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertSame('D002', (string) $persistedDeceased->getId());
    }

    public function testItReturnsNullIfADeceasedIsNotFoundById(): void
    {
        $deceased = $this->repo->findById(new DeceasedId('unknown_id'));
        $this->assertNull($deceased);
    }
}
