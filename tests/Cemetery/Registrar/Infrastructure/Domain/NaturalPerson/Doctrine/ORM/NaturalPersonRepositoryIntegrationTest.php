<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM\NaturalPersonRepository as DoctrineORMNaturalPersonRepository;
use Cemetery\Tests\Registrar\Domain\NaturalPerson\NaturalPersonProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRepositoryIntegrationTest extends KernelTestCase
{
    private NaturalPerson $naturalPersonA;

    private NaturalPerson $naturalPersonB;

    private NaturalPerson $naturalPersonC;

    private EntityManagerInterface $entityManager;

    private DoctrineORMNaturalPersonRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->naturalPersonA = NaturalPersonProvider::getNaturalPersonA();
        $this->naturalPersonB = NaturalPersonProvider::getNaturalPersonB();
        $this->naturalPersonC = NaturalPersonProvider::getNaturalPersonC();
        $this->entityManager  = $container->get(EntityManagerInterface::class);
        $this->repo           = new DoctrineORMNaturalPersonRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewNaturalPerson(): void
    {
        $this->repo->save($this->naturalPersonA);
        $this->entityManager->clear();

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->getId());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertSame((string) $this->naturalPersonA->getId(), (string) $persistedNaturalPerson->getId());
        $this->assertSame((string) $this->naturalPersonA->getFullName(), (string) $persistedNaturalPerson->getFullName());
        $this->assertNull($persistedNaturalPerson->getBornAt());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItUpdatesAnExistingNaturalPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->naturalPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->getId());
        $bornAt                 = new \DateTimeImmutable('2003-03-01');
        $persistedNaturalPerson->setBornAt($bornAt);
        $this->repo->save($persistedNaturalPerson);
        $this->entityManager->clear();

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->getBornAt());
        $this->assertSame('2003-03-01', $persistedNaturalPerson->getBornAt()->format('Y-m-d'));
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItSavesACollectionOfNewNaturalPersons(): void
    {
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB, $this->naturalPersonC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->naturalPersonA->getId()));
        $this->assertNotNull($this->repo->findById($this->naturalPersonB->getId()));
        $this->assertNotNull($this->repo->findById($this->naturalPersonC->getId()));
        $this->assertSame(3, $this->getRowCount());
    }

    public function testItUpdatesExistingNaturalPersonWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount());

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->getId());
        $bornAt                 = new \DateTimeImmutable('2003-03-01');
        $persistedNaturalPerson->setBornAt($bornAt);
        $this->repo->saveAll(new NaturalPersonCollection([$persistedNaturalPerson, $this->naturalPersonC]));
        $this->entityManager->clear();

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->getBornAt());
        $this->assertSame('2003-03-01', $persistedNaturalPerson->getBornAt()->format('Y-m-d'));

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonB->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedNaturalPerson->getId());
        $this->assertSame('NP002', (string) $persistedNaturalPerson->getId());
        $this->assertInstanceOf(FullName::class, $persistedNaturalPerson->getFullName());
        $this->assertSame('Petrov Petr Petrovich', (string) $persistedNaturalPerson->getFullName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->getBornAt());
        $this->assertSame('1998-12-30', $persistedNaturalPerson->getBornAt()->format('Y-m-d'));

        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonC->getId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedNaturalPerson->getId());
        $this->assertSame('NP003', (string) $persistedNaturalPerson->getId());
        $this->assertInstanceOf(FullName::class, $persistedNaturalPerson->getFullName());
        $this->assertSame('Sidorov Sidr Sidorovich', (string) $persistedNaturalPerson->getFullName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedNaturalPerson->getBornAt());
        $this->assertSame('2005-05-20', $persistedNaturalPerson->getBornAt()->format('Y-m-d'));

        $this->assertSame(3, $this->getRowCount());
    }

    public function testItRemovesANaturalPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->naturalPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonA->getId());
        $this->repo->remove($persistedNaturalPerson);
        $this->assertSame(0, $this->getRowCount());
    }

    public function testItRemovesACollectionOfNaturalPersons(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB, $this->naturalPersonC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount());

        // Testing itself
        $persistedNaturalPersonB = $this->repo->findById($this->naturalPersonB->getId());
        $persistedNaturalPersonC = $this->repo->findById($this->naturalPersonC->getId());
        $this->repo->removeAll(new NaturalPersonCollection([$persistedNaturalPersonB, $persistedNaturalPersonC]));
        $this->assertSame(1, $this->getRowCount());
        $this->assertNotNull($this->repo->findById($this->naturalPersonA->getId()));
    }

    public function testItFindsANaturalPersonById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([$this->naturalPersonA, $this->naturalPersonB, $this->naturalPersonC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedNaturalPerson = $this->repo->findById($this->naturalPersonB->getId());
        $this->assertInstanceOf(NaturalPerson::class, $persistedNaturalPerson);
        $this->assertSame((string) $this->naturalPersonB->getId(), (string) $persistedNaturalPerson->getId());
    }

    public function testItReturnsNullIfANaturalPersonIsNotFoundById(): void
    {
        $naturalPerson = $this->repo->findById(new NaturalPersonId('unknown_id'));

        $this->assertNull($naturalPerson);
    }

    private function truncateEntities(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    private function getRowCount(): int
    {
        return (int) $this->entityManager
            ->getRepository(NaturalPerson::class)
            ->createQueryBuilder('np')
            ->select('COUNT(np.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
