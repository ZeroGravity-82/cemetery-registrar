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

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertSame('JP001', (string) $persistedJuristicPerson->id());
        $this->assertSame('ООО "Рога и копыта"', (string) $persistedJuristicPerson->name());
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
        $inn                     = new Inn('7728168971');
        $persistedJuristicPerson->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedJuristicPerson);
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->inn());
        $this->assertSame('7728168971', (string) $persistedJuristicPerson->inn());
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
        $inn                     = new Inn('7728168971');
        $persistedJuristicPerson->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new JuristicPersonCollection([$persistedJuristicPerson, $this->juristicPersonC]));
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->inn());
        $this->assertSame('7728168971', (string) $persistedJuristicPerson->inn());
        $this->assertTrue($this->juristicPersonA->updatedAt() < $persistedJuristicPerson->updatedAt());

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonC->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(JuristicPersonId::class, $persistedJuristicPerson->id());
        $this->assertSame('JP003', (string) $persistedJuristicPerson->id());
        $this->assertInstanceOf(Name::class, $persistedJuristicPerson->name());
        $this->assertSame('ПАО "ГАЗПРОМ"', (string) $persistedJuristicPerson->name());
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->inn());
        $this->assertSame('7736050003', (string) $persistedJuristicPerson->inn());

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
        $this->assertSame('АО "АЛЬФА-БАНК"', (string) $persistedJuristicPerson->bankDetails()->bankName());
        $this->assertSame('044525593', (string) $persistedJuristicPerson->bankDetails()->bik());
        $this->assertSame('30101810200000000593', (string) $persistedJuristicPerson->bankDetails()->correspondentAccount());
        $this->assertSame('40701810401400000014', (string) $persistedJuristicPerson->bankDetails()->currentAccount());
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
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonA->id()));
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
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonB->id()));
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonC->id()));
        $this->assertNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonA->id()));
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
        $this->assertSame('JP002', (string) $persistedJuristicPerson->id());
    }

    public function testItReturnsNullIfAJuristicPersonIsNotFoundById(): void
    {
        $juristicPerson = $this->repo->findById(new JuristicPersonId('unknown_id'));
        $this->assertNull($juristicPerson);
    }
}
