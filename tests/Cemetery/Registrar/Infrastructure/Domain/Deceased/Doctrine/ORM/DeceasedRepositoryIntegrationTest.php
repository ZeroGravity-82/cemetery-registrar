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

        $this->deceasedA = DeceasedProvider::getDeceasedA();
        $this->deceasedB = DeceasedProvider::getDeceasedB();
        $this->deceasedC = DeceasedProvider::getDeceasedC();
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineOrmDeceasedRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewDeceased(): void
    {
        $this->repo->save($this->deceasedA);
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertSame('D001', (string) $persistedDeceased->id());
        $this->assertSame('NP001', (string) $persistedDeceased->naturalPersonId());
        $this->assertSame(
            $this->deceasedA->diedAt()->format('Y-m-d'),
            $persistedDeceased->diedAt()->format('Y-m-d')
        );
        $this->assertNull($persistedDeceased->deathCertificateId());
        $this->assertNull($persistedDeceased->causeOfDeath());
        $this->assertSame(1, $this->getRowCount(Deceased::class));
        $this->assertSame(
            $this->deceasedA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedDeceased->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->deceasedA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedDeceased->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedDeceased->removedAt());
    }

    public function testItUpdatesAnExistingDeceased(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->deceasedA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedA->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Некоторая причина смерти 1');
        $persistedDeceased->setDeathCertificateId($deathCertificateId);
        $persistedDeceased->setCauseOfDeath($causeOfDeath);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedDeceased);
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->deathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->deathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->causeOfDeath());
        $this->assertSame('Некоторая причина смерти 1', (string) $persistedDeceased->causeOfDeath());
        $this->assertSame(1, $this->getRowCount(Deceased::class));
        $this->assertSame(
            $this->deceasedA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedDeceased->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->deceasedA->updatedAt() < $persistedDeceased->updatedAt());
        $this->assertNull($persistedDeceased->removedAt());
    }

    public function testItSavesACollectionOfNewDeceaseds(): void
    {
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->deceasedA->id()));
        $this->assertNotNull($this->repo->findById($this->deceasedB->id()));
        $this->assertNotNull($this->repo->findById($this->deceasedC->id()));
        $this->assertSame(3, $this->getRowCount(Deceased::class));
    }

    public function testItUpdatesExistingDeceasedWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedA->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $deathCertificateId = new DeathCertificateId('DC001');
        $causeOfDeath       = new CauseOfDeath('Некоторая причина смерти 1');
        $persistedDeceased->setDeathCertificateId($deathCertificateId);
        $persistedDeceased->setCauseOfDeath($causeOfDeath);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new DeceasedCollection([$persistedDeceased, $this->deceasedC]));
        $this->entityManager->clear();

        $persistedDeceased = $this->repo->findById($this->deceasedA->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->deathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->deathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->causeOfDeath());
        $this->assertSame('Некоторая причина смерти 1', (string) $persistedDeceased->causeOfDeath());
        $this->assertTrue($this->deceasedA->updatedAt() < $persistedDeceased->updatedAt());

        $persistedDeceased = $this->repo->findById($this->deceasedB->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeceasedId::class, $persistedDeceased->id());
        $this->assertSame('D002', (string) $persistedDeceased->id());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedDeceased->naturalPersonId());
        $this->assertSame('NP002', (string) $persistedDeceased->naturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedDeceased->diedAt());
        $this->assertSame('2001-02-11', $persistedDeceased->diedAt()->format('Y-m-d'));
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->deathCertificateId());
        $this->assertSame('DC001', (string) $persistedDeceased->deathCertificateId());
        $this->assertInstanceOf(CauseOfDeath::class, $persistedDeceased->causeOfDeath());
        $this->assertSame('Некоторая причина смерти 1', (string) $persistedDeceased->causeOfDeath());

        $persistedDeceased = $this->repo->findById($this->deceasedC->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertInstanceOf(DeceasedId::class, $persistedDeceased->id());
        $this->assertSame('D003', (string) $persistedDeceased->id());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedDeceased->naturalPersonId());
        $this->assertSame('NP003', (string) $persistedDeceased->naturalPersonId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedDeceased->diedAt());
        $this->assertSame('2011-05-13', $persistedDeceased->diedAt()->format('Y-m-d'));
        $this->assertInstanceOf(DeathCertificateId::class, $persistedDeceased->deathCertificateId());
        $this->assertSame('DC002', (string) $persistedDeceased->deathCertificateId());
        $this->assertNull($persistedDeceased->causeOfDeath());

        $this->assertSame(3, $this->getRowCount(Deceased::class));
    }

    public function testItRemovesADeceased(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->deceasedA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedA->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->repo->remove($persistedDeceased);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->deceasedA->id()));
        $this->assertSame(1, $this->getRowCount(Deceased::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedA->id()));
    }

    public function testItRemovesACollectionOfDeceaseds(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(Deceased::class));

        // Testing itself
        $persistedDeceasedB = $this->repo->findById($this->deceasedB->id());
        $persistedDeceasedC = $this->repo->findById($this->deceasedC->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceasedB);
        $this->assertInstanceOf(Deceased::class, $persistedDeceasedC);
        $this->repo->removeAll(new DeceasedCollection([$persistedDeceasedB, $persistedDeceasedC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->deceasedB->id()));
        $this->assertNull($this->repo->findById($this->deceasedC->id()));
        $this->assertNotNull($this->repo->findById($this->deceasedA->id()));
        $this->assertSame(3, $this->getRowCount(Deceased::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedB->id()));
        $this->assertNotNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedC->id()));
        $this->assertNull($this->getRemovedAtTimestampById(Deceased::class, (string) $this->deceasedA->id()));
    }

    public function testItFindsADeceasedById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new DeceasedCollection([$this->deceasedA, $this->deceasedB, $this->deceasedC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedDeceased = $this->repo->findById($this->deceasedB->id());
        $this->assertInstanceOf(Deceased::class, $persistedDeceased);
        $this->assertSame('D002', (string) $persistedDeceased->id());
    }

    public function testItReturnsNullIfADeceasedIsNotFoundById(): void
    {
        $deceased = $this->repo->findById(new DeceasedId('unknown_id'));
        $this->assertNull($deceased);
    }
}
