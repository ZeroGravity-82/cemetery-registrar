<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM\JuristicPersonRepository as DoctrineORMJuristicPersonRepository;
use Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson\JuristicPersonProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRepositoryIntegrationTest extends KernelTestCase
{
    private JuristicPerson $juristicPersonA;

    private JuristicPerson $juristicPersonB;

    private JuristicPerson $juristicPersonC;

    private EntityManagerInterface $entityManager;

    private DoctrineORMJuristicPersonRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->juristicPersonA = JuristicPersonProvider::getJuristicPersonA();
        $this->juristicPersonB = JuristicPersonProvider::getJuristicPersonB();
        $this->juristicPersonC = JuristicPersonProvider::getJuristicPersonC();
        $this->entityManager   = $container->get(EntityManagerInterface::class);
        $this->repo            = new DoctrineORMJuristicPersonRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewJuristicPerson(): void
    {
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertSame((string) $this->juristicPersonA->getId(), (string) $persistedJuristicPerson->getId());
        $this->assertSame((string) $this->juristicPersonA->getName(), (string) $persistedJuristicPerson->getName());
        $this->assertNull($persistedJuristicPerson->getInn());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItUpdatesAnExistingJuristicPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $inn                     = new Inn('7728168971');
        $persistedJuristicPerson->setInn($inn);
        $this->repo->save($persistedJuristicPerson);
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->getInn());
        $this->assertSame('7728168971', (string) $persistedJuristicPerson->getInn());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItSavesACollectionOfNewJuristicPersons(): void
    {
        $this->repo->saveAll(
            new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB, $this->juristicPersonC])
        );
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->juristicPersonA->getId()));
        $this->assertNotNull($this->repo->findById($this->juristicPersonB->getId()));
        $this->assertNotNull($this->repo->findById($this->juristicPersonC->getId()));
        $this->assertSame(3, $this->getRowCount());
    }

    public function testItUpdatesExistingJuristicPersonWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount());

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $inn                     = new Inn('7728168971');
        $persistedJuristicPerson->setInn($inn);
        $this->repo->saveAll(new JuristicPersonCollection([$persistedJuristicPerson, $this->juristicPersonC]));
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->getInn());
        $this->assertSame('7728168971', (string) $persistedJuristicPerson->getInn());

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonB->getId());
        $this->assertInstanceOf(JuristicPersonId::class, $persistedJuristicPerson->getId());
        $this->assertSame('JP002', (string) $persistedJuristicPerson->getId());
        $this->assertInstanceOf(Name::class, $persistedJuristicPerson->getName());
        $this->assertSame('ООО Ромашка', (string) $persistedJuristicPerson->getName());
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->getInn());
        $this->assertSame('5404447629', (string) $persistedJuristicPerson->getInn());

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonC->getId());
        $this->assertInstanceOf(JuristicPersonId::class, $persistedJuristicPerson->getId());
        $this->assertSame('JP003', (string) $persistedJuristicPerson->getId());
        $this->assertInstanceOf(Name::class, $persistedJuristicPerson->getName());
        $this->assertSame('ПАО "ГАЗПРОМ"', (string) $persistedJuristicPerson->getName());
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->getInn());
        $this->assertSame('7736050003', (string) $persistedJuristicPerson->getInn());

        $this->assertSame(3, $this->getRowCount());
    }

    public function testItRemovesAJuristicPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->repo->remove($persistedJuristicPerson);
        $this->assertSame(0, $this->getRowCount());
    }

    public function testItRemovesACollectionOfJuristicPersons(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB, $this->juristicPersonC])
        );
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount());

        // Testing itself
        $persistedJuristicPersonB = $this->repo->findById($this->juristicPersonB->getId());
        $persistedJuristicPersonC = $this->repo->findById($this->juristicPersonC->getId());
        $this->repo->removeAll(new JuristicPersonCollection([$persistedJuristicPersonB, $persistedJuristicPersonC]));
        $this->assertSame(1, $this->getRowCount());
        $this->assertNotNull($this->repo->findById($this->juristicPersonA->getId()));
    }

    public function testItFindsAJuristicPersonById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB, $this->juristicPersonC])
        );
        $this->entityManager->clear();

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonB->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertSame((string) $this->juristicPersonB->getId(), (string) $persistedJuristicPerson->getId());
    }

    public function testItReturnsNullIfAJuristicPersonIsNotFoundById(): void
    {
        $juristicPerson = $this->repo->findById(new JuristicPersonId('unknown_id'));

        $this->assertNull($juristicPerson);
    }

    private function truncateEntities(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    private function getRowCount(): int
    {
        return (int) $this->entityManager
            ->getRepository(JuristicPerson::class)
            ->createQueryBuilder('jp')
            ->select('COUNT(jp.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
