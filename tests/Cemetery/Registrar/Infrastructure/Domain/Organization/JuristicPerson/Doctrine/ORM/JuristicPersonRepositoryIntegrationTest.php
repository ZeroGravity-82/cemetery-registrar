<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM\JuristicPersonRepository as DoctrineORMJuristicPersonRepository;
use Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson\JuristicPersonProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private JuristicPerson                      $juristicPersonA;
    private JuristicPerson                      $juristicPersonB;
    private JuristicPerson                      $juristicPersonC;
    private DoctrineORMJuristicPersonRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->juristicPersonA = JuristicPersonProvider::getJuristicPersonA();
        $this->juristicPersonB = JuristicPersonProvider::getJuristicPersonB();
        $this->juristicPersonC = JuristicPersonProvider::getJuristicPersonC();
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineORMJuristicPersonRepository($this->entityManager);
        $this->truncateEntities();
    }

    public function testItSavesANewJuristicPerson(): void
    {
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertTrue($persistedJuristicPerson->id()->isEqual($this->juristicPersonA->id()));
        $this->assertTrue($persistedJuristicPerson->name()->isEqual($this->juristicPersonA->name()));
        $this->assertNull($persistedJuristicPerson->inn());
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));
        $this->assertSame(
            $this->juristicPersonA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedJuristicPerson->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->juristicPersonA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedJuristicPerson->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedJuristicPerson->removedAt());
    }

    public function testItUpdatesAnExistingJuristicPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $newInn = new Inn('7728168971');
        $persistedJuristicPerson->setInn($newInn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedJuristicPerson);
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->inn());
        $this->assertTrue($persistedJuristicPerson->inn()->isEqual($newInn));
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));
        $this->assertSame(
            $this->juristicPersonA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedJuristicPerson->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->juristicPersonA->updatedAt() < $persistedJuristicPerson->updatedAt());
        $this->assertNull($persistedJuristicPerson->removedAt());
    }

    public function testItSavesACollectionOfNewJuristicPersons(): void
    {
        $this->repo->saveAll(
            new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB, $this->juristicPersonC])
        );
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->juristicPersonA->id()));
        $this->assertNotNull($this->repo->findById($this->juristicPersonB->id()));
        $this->assertNotNull($this->repo->findById($this->juristicPersonC->id()));
        $this->assertSame(3, $this->getRowCount(JuristicPerson::class));
    }

    public function testItUpdatesExistingJuristicPersonWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $newInn = new Inn('7728168971');
        $persistedJuristicPerson->setInn($newInn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new JuristicPersonCollection([$persistedJuristicPerson, $this->juristicPersonC]));
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->inn());
        $this->assertTrue($persistedJuristicPerson->inn()->isEqual($newInn));
        $this->assertTrue($this->juristicPersonA->updatedAt() < $persistedJuristicPerson->updatedAt());

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonC->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(JuristicPersonId::class, $persistedJuristicPerson->id());
        $this->assertTrue($persistedJuristicPerson->id()->isEqual($this->juristicPersonC->id()));
        $this->assertInstanceOf(Name::class, $persistedJuristicPerson->name());
        $this->assertTrue($persistedJuristicPerson->name()->isEqual($this->juristicPersonC->name()));
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->inn());
        $this->assertTrue($persistedJuristicPerson->inn()->isEqual($this->juristicPersonC->inn()));

        $this->assertSame(2, $this->getRowCount(JuristicPerson::class));
    }

    public function testItHydratesBankDetailsEmbeddable(): void
    {
        $this->repo->saveAll(new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB]));
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertNull($persistedJuristicPerson->bankDetails());

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonB->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(BankDetails::class, $persistedJuristicPerson->bankDetails());
        $this->assertTrue($persistedJuristicPerson->bankDetails()->isEqual($this->juristicPersonB->bankDetails()));
    }

    public function testItRemovesAJuristicPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->repo->remove($persistedJuristicPerson);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->juristicPersonA->id()));
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, $this->juristicPersonA->id()->value()));
    }

    public function testItRemovesACollectionOfJuristicPersons(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB, $this->juristicPersonC])
        );
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(JuristicPerson::class));

        // Testing itself
        $persistedJuristicPersonB = $this->repo->findById($this->juristicPersonB->id());
        $persistedJuristicPersonC = $this->repo->findById($this->juristicPersonC->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPersonB);
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPersonC);
        $this->repo->removeAll(new JuristicPersonCollection([$persistedJuristicPersonB, $persistedJuristicPersonC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->juristicPersonB->id()));
        $this->assertNull($this->repo->findById($this->juristicPersonC->id()));
        $this->assertNotNull($this->repo->findById($this->juristicPersonA->id()));
        $this->assertSame(3, $this->getRowCount(JuristicPerson::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, $this->juristicPersonB->id()->value()));
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, $this->juristicPersonC->id()->value()));
        $this->assertNull($this->getRemovedAtTimestampById(JuristicPerson::class, $this->juristicPersonA->id()->value()));
    }

    public function testItFindsAJuristicPersonById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB, $this->juristicPersonC])
        );
        $this->entityManager->clear();

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonB->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertTrue($persistedJuristicPerson->id()->isEqual($this->juristicPersonB->id()));
    }

    public function testItReturnsNullIfAJuristicPersonIsNotFoundById(): void
    {
        $juristicPerson = $this->repo->findById(new JuristicPersonId('unknown_id'));
        $this->assertNull($juristicPerson);
    }
}
