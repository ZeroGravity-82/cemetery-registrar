<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM\NaturalPersonRepository as DoctrineORMNaturalPersonRepository;
use Cemetery\Tests\Registrar\Domain\NaturalPerson\NaturalPersonProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private NaturalPerson                      $naturalPersonA;
    private NaturalPerson                      $naturalPersonB;
    private NaturalPerson                      $naturalPersonC;
    private DoctrineORMNaturalPersonRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->naturalPersonA = NaturalPersonProvider::getNaturalPersonA();
        $this->naturalPersonB = NaturalPersonProvider::getNaturalPersonB();
        $this->naturalPersonC = NaturalPersonProvider::getNaturalPersonC();
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineORMNaturalPersonRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewNaturalPerson(): void
    {
        $this->repo->save($this->naturalPersonA);
        $this->entityManager->clear();

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertSame('NP001', (string) $persistedNaturalPerson->id());
        $this->assertSame('Иванов Иван Иванович', (string) $persistedNaturalPerson->fullName());
        $this->assertNull($persistedNaturalPerson->bornAt());
        $this->assertSame(1, $this->getRowCount(NaturalPerson::class));
        $this->assertSame(
            $this->naturalPersonA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedNaturalPerson->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->naturalPersonA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedNaturalPerson->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedNaturalPerson->removedAt());
    }

    public function testItUpdatesAnExistingNaturalPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->naturalPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(NaturalPerson::class));

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $bornAt                 = new \DateTimeImmutable('2003-03-01');
        $persistedNaturalPerson->setBornAt($bornAt);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedNaturalPerson);
        $this->entityManager->clear();

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->bornAt());
        $this->assertSame('2003-03-01', $persistedNaturalPerson->bornAt()->format('Y-m-d'));
        $this->assertSame(1, $this->getRowCount(NaturalPerson::class));
        $this->assertSame(
            $this->naturalPersonA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedNaturalPerson->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->naturalPersonA->updatedAt() < $persistedNaturalPerson->updatedAt());
        $this->assertNull($persistedNaturalPerson->removedAt());
    }

    public function testItSavesACollectionOfNewNaturalPersons(): void
    {
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB, $this->naturalPersonC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->naturalPersonA->id()));
        $this->assertNotNull($this->repo->findById($this->naturalPersonB->id()));
        $this->assertNotNull($this->repo->findById($this->naturalPersonC->id()));
        $this->assertSame(3, $this->getRowCount(NaturalPerson::class));
    }

    public function testItUpdatesExistingNaturalPersonWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(NaturalPerson::class));

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $bornAt                 = new \DateTimeImmutable('2003-03-01');
        $persistedNaturalPerson->setBornAt($bornAt);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new NaturalPersonCollection([$persistedNaturalPerson, $this->naturalPersonC]));
        $this->entityManager->clear();

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->bornAt());
        $this->assertSame('2003-03-01', $persistedNaturalPerson->bornAt()->format('Y-m-d'));
        $this->assertTrue($this->naturalPersonA->updatedAt() < $persistedNaturalPerson->updatedAt());

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonB->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertInstanceOf(NaturalPersonId::class, $persistedNaturalPerson->id());
        $this->assertSame('NP002', (string) $persistedNaturalPerson->id());
        $this->assertInstanceOf(FullName::class, $persistedNaturalPerson->fullName());
        $this->assertSame('Петров Пётр Петрович', (string) $persistedNaturalPerson->fullName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->bornAt());
        $this->assertSame('1998-12-30', $persistedNaturalPerson->bornAt()->format('Y-m-d'));

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonC->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertInstanceOf(NaturalPersonId::class, $persistedNaturalPerson->id());
        $this->assertSame('NP003', (string) $persistedNaturalPerson->id());
        $this->assertInstanceOf(FullName::class, $persistedNaturalPerson->fullName());
        $this->assertSame('Сидоров Сидр Сидорович', (string) $persistedNaturalPerson->fullName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->bornAt());
        $this->assertSame('2005-05-20', $persistedNaturalPerson->bornAt()->format('Y-m-d'));

        $this->assertSame(3, $this->getRowCount(NaturalPerson::class));
    }

    public function testItRemovesANaturalPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->naturalPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(NaturalPerson::class));

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->repo->remove($persistedNaturalPerson);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->naturalPersonA->id()));
        $this->assertSame(1, $this->getRowCount(NaturalPerson::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(NaturalPerson::class, (string) $this->naturalPersonA->id()));
    }

    public function testItRemovesACollectionOfNaturalPersons(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB, $this->naturalPersonC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(NaturalPerson::class));

        // Testing itself
        $persistedNaturalPersonB = $this->repo->findById($this->naturalPersonB->id());
        $persistedNaturalPersonC = $this->repo->findById($this->naturalPersonC->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPersonB);
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPersonC);
        $this->repo->removeAll(new NaturalPersonCollection([$persistedNaturalPersonB, $persistedNaturalPersonC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->naturalPersonB->id()));
        $this->assertNull($this->repo->findById($this->naturalPersonC->id()));
        $this->assertNotNull($this->repo->findById($this->naturalPersonA->id()));
        $this->assertSame(3, $this->getRowCount(NaturalPerson::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(NaturalPerson::class, (string) $this->naturalPersonB->id()));
        $this->assertNotNull($this->getRemovedAtTimestampById(NaturalPerson::class, (string) $this->naturalPersonC->id()));
        $this->assertNull($this->getRemovedAtTimestampById(NaturalPerson::class, (string) $this->naturalPersonA->id()));
    }

    public function testItFindsANaturalPersonById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB, $this->naturalPersonC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonB->id());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertSame('NP002', (string) $persistedNaturalPerson->id());
    }

    public function testItReturnsNullIfANaturalPersonIsNotFoundById(): void
    {
        $naturalPerson = $this->repo->findById(new NaturalPersonId('unknown_id'));
        $this->assertNull($naturalPerson);
    }
}
