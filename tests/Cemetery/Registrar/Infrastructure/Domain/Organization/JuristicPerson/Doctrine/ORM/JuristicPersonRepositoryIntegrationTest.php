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

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertSame('JP001', (string) $persistedJuristicPerson->getId());
        $this->assertSame('ООО "Рога и копыта"', (string) $persistedJuristicPerson->getName());
        $this->assertNull($persistedJuristicPerson->getInn());
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));
        $this->assertSame(
            $this->juristicPersonA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedJuristicPerson->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->juristicPersonA->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            $persistedJuristicPerson->getUpdatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedJuristicPerson->getRemovedAt());
    }

    public function testItUpdatesAnExistingJuristicPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $inn                     = new Inn('7728168971');
        $persistedJuristicPerson->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedJuristicPerson);
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->getInn());
        $this->assertSame('7728168971', (string) $persistedJuristicPerson->getInn());
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));
        $this->assertSame(
            $this->juristicPersonA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedJuristicPerson->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->juristicPersonA->getUpdatedAt() < $persistedJuristicPerson->getUpdatedAt());
        $this->assertNull($persistedJuristicPerson->getRemovedAt());
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
        $this->assertSame(3, $this->getRowCount(JuristicPerson::class));
    }

    public function testItUpdatesExistingJuristicPersonWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $inn                     = new Inn('7728168971');
        $persistedJuristicPerson->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new JuristicPersonCollection([$persistedJuristicPerson, $this->juristicPersonC]));
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->getInn());
        $this->assertSame('7728168971', (string) $persistedJuristicPerson->getInn());
        $this->assertTrue($this->juristicPersonA->getUpdatedAt() < $persistedJuristicPerson->getUpdatedAt());

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonC->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(JuristicPersonId::class, $persistedJuristicPerson->getId());
        $this->assertSame('JP003', (string) $persistedJuristicPerson->getId());
        $this->assertInstanceOf(Name::class, $persistedJuristicPerson->getName());
        $this->assertSame('ПАО "ГАЗПРОМ"', (string) $persistedJuristicPerson->getName());
        $this->assertInstanceOf(Inn::class, $persistedJuristicPerson->getInn());
        $this->assertSame('7736050003', (string) $persistedJuristicPerson->getInn());

        $this->assertSame(2, $this->getRowCount(JuristicPerson::class));
    }

    public function testItHydratesBankDetailsEmbeddable(): void
    {
        $this->repo->saveAll(new JuristicPersonCollection([$this->juristicPersonA, $this->juristicPersonB]));
        $this->entityManager->clear();

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertNull($persistedJuristicPerson->getBankDetails());

        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonB->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->assertInstanceOf(BankDetails::class, $persistedJuristicPerson->getBankDetails());
        $this->assertSame('АО "АЛЬФА-БАНК"', (string) $persistedJuristicPerson->getBankDetails()->getBankName());
        $this->assertSame('044525593', (string) $persistedJuristicPerson->getBankDetails()->getBik());
        $this->assertSame('30101810200000000593', (string) $persistedJuristicPerson->getBankDetails()->getCorrespondentAccount());
        $this->assertSame('40701810401400000014', (string) $persistedJuristicPerson->getBankDetails()->getCurrentAccount());
    }

    public function testItRemovesAJuristicPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->juristicPersonA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));

        // Testing itself
        $persistedJuristicPerson = $this->repo->findById($this->juristicPersonA->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPerson);
        $this->repo->remove($persistedJuristicPerson);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->juristicPersonA->getId()));
        $this->assertSame(1, $this->getRowCount(JuristicPerson::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonA->getId()));
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
        $persistedJuristicPersonB = $this->repo->findById($this->juristicPersonB->getId());
        $persistedJuristicPersonC = $this->repo->findById($this->juristicPersonC->getId());
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPersonB);
        $this->assertInstanceOf(JuristicPerson::class, $persistedJuristicPersonC);
        $this->repo->removeAll(new JuristicPersonCollection([$persistedJuristicPersonB, $persistedJuristicPersonC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->juristicPersonB->getId()));
        $this->assertNull($this->repo->findById($this->juristicPersonC->getId()));
        $this->assertNotNull($this->repo->findById($this->juristicPersonA->getId()));
        $this->assertSame(3, $this->getRowCount(JuristicPerson::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonB->getId()));
        $this->assertNotNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonC->getId()));
        $this->assertNull($this->getRemovedAtTimestampById(JuristicPerson::class, (string) $this->juristicPersonA->getId()));
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
        $this->assertSame('JP002', (string) $persistedJuristicPerson->getId());
    }

    public function testItReturnsNullIfAJuristicPersonIsNotFoundById(): void
    {
        $juristicPerson = $this->repo->findById(new JuristicPersonId('unknown_id'));
        $this->assertNull($juristicPerson);
    }
}
