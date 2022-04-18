<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\ORM;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\ORM\SoleProprietorRepository as DoctrineORMSoleProprietorRepository;
use Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor\SoleProprietorProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private SoleProprietor                      $soleProprietorA;
    private SoleProprietor                      $soleProprietorB;
    private SoleProprietor                      $soleProprietorC;
    private DoctrineORMSoleProprietorRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->soleProprietorA = SoleProprietorProvider::getSoleProprietorA();
        $this->soleProprietorB = SoleProprietorProvider::getSoleProprietorB();
        $this->soleProprietorC = SoleProprietorProvider::getSoleProprietorC();
        $this->entityManager   = $container->get(EntityManagerInterface::class);
        $this->repo            = new DoctrineORMSoleProprietorRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewSoleProprietor(): void
    {
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertSame('SP001', (string) $persistedSoleProprietor->id());
        $this->assertSame('ИП Иванов Иван Иванович', (string) $persistedSoleProprietor->name());
        $this->assertNull($persistedSoleProprietor->inn());
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));
        $this->assertSame(
            $this->soleProprietorA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedSoleProprietor->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->soleProprietorA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedSoleProprietor->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedSoleProprietor->removedAt());
    }

    public function testItUpdatesAnExistingSoleProprietor(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $inn                     = new Inn('772208786091');
        $persistedSoleProprietor->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedSoleProprietor);
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->inn());
        $this->assertSame('772208786091', (string) $persistedSoleProprietor->inn());
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));
        $this->assertSame(
            $this->soleProprietorA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedSoleProprietor->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->soleProprietorA->updatedAt() < $persistedSoleProprietor->updatedAt());
        $this->assertNull($persistedSoleProprietor->removedAt());
    }

    public function testItSavesACollectionOfNewSoleProprietors(): void
    {
        $this->repo->saveAll(
            new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB, $this->soleProprietorC])
        );
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->soleProprietorA->id()));
        $this->assertNotNull($this->repo->findById($this->soleProprietorB->id()));
        $this->assertNotNull($this->repo->findById($this->soleProprietorC->id()));
        $this->assertSame(3, $this->getRowCount(SoleProprietor::class));
    }

    public function testItUpdatesExistingSoleProprietorWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $inn                     = new Inn('540701117479');
        $persistedSoleProprietor->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new SoleProprietorCollection([$persistedSoleProprietor, $this->soleProprietorC]));
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->inn());
        $this->assertSame('540701117479', (string) $persistedSoleProprietor->inn());
        $this->assertTrue($this->soleProprietorA->updatedAt() < $persistedSoleProprietor->updatedAt());

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorC->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(SoleProprietorId::class, $persistedSoleProprietor->id());
        $this->assertSame('SP003', (string) $persistedSoleProprietor->id());
        $this->assertInstanceOf(Name::class, $persistedSoleProprietor->name());
        $this->assertSame('ИП Сидоров Сидр Сидорович', (string) $persistedSoleProprietor->name());
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->inn());
        $this->assertSame('391600743661', (string) $persistedSoleProprietor->inn());

        $this->assertSame(2, $this->getRowCount(SoleProprietor::class));
    }

    public function testItHydratesBankDetailsEmbeddable(): void
    {
        $this->repo->saveAll(new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB]));
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertNull($persistedSoleProprietor->bankDetails());

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorB->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(BankDetails::class, $persistedSoleProprietor->bankDetails());
        $this->assertSame('АО "АЛЬФА-БАНК"', (string) $persistedSoleProprietor->bankDetails()->bankName());
        $this->assertSame('044525593', (string) $persistedSoleProprietor->bankDetails()->bik());
        $this->assertSame('30101810200000000593', (string) $persistedSoleProprietor->bankDetails()->correspondentAccount());
        $this->assertSame('40701810401400000014', (string) $persistedSoleProprietor->bankDetails()->currentAccount());
    }

    public function testItRemovesASoleProprietor(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->repo->remove($persistedSoleProprietor);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->soleProprietorA->id()));
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorA->id()));
    }

    public function testItRemovesACollectionOfSoleProprietors(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB, $this->soleProprietorC])
        );
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(SoleProprietor::class));

        // Testing itself
        $persistedSoleProprietorB = $this->repo->findById($this->soleProprietorB->id());
        $persistedSoleProprietorC = $this->repo->findById($this->soleProprietorC->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietorB);
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietorC);
        $this->repo->removeAll(new SoleProprietorCollection([$persistedSoleProprietorB, $persistedSoleProprietorC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->soleProprietorB->id()));
        $this->assertNull($this->repo->findById($this->soleProprietorC->id()));
        $this->assertNotNull($this->repo->findById($this->soleProprietorA->id()));
        $this->assertSame(3, $this->getRowCount(SoleProprietor::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorB->id()));
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorC->id()));
        $this->assertNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorA->id()));
    }

    public function testItFindsASoleProprietorById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB, $this->soleProprietorC])
        );
        $this->entityManager->clear();

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorB->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertSame('SP002', (string) $persistedSoleProprietor->id());
    }

    public function testItReturnsNullIfASoleProprietorIsNotFoundById(): void
    {
        $soleProprietor = $this->repo->findById(new SoleProprietorId('unknown_id'));
        $this->assertNull($soleProprietor);
    }
}
