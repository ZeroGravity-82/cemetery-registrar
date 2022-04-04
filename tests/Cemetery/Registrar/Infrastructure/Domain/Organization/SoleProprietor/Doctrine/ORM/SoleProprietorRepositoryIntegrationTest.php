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

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertSame('SP001', (string) $persistedSoleProprietor->getId());
        $this->assertSame('ИП Иванов Иван Иванович', (string) $persistedSoleProprietor->getName());
        $this->assertNull($persistedSoleProprietor->getInn());
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));
        $this->assertSame(
            $this->soleProprietorA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedSoleProprietor->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->soleProprietorA->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            $persistedSoleProprietor->getUpdatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedSoleProprietor->getRemovedAt());
    }

    public function testItUpdatesAnExistingSoleProprietor(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $inn                     = new Inn('772208786091');
        $persistedSoleProprietor->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedSoleProprietor);
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->getInn());
        $this->assertSame('772208786091', (string) $persistedSoleProprietor->getInn());
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));
        $this->assertSame(
            $this->soleProprietorA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedSoleProprietor->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->soleProprietorA->getUpdatedAt() < $persistedSoleProprietor->getUpdatedAt());
        $this->assertNull($persistedSoleProprietor->getRemovedAt());
    }

    public function testItSavesACollectionOfNewSoleProprietors(): void
    {
        $this->repo->saveAll(
            new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB, $this->soleProprietorC])
        );
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->soleProprietorA->getId()));
        $this->assertNotNull($this->repo->findById($this->soleProprietorB->getId()));
        $this->assertNotNull($this->repo->findById($this->soleProprietorC->getId()));
        $this->assertSame(3, $this->getRowCount(SoleProprietor::class));
    }

    public function testItUpdatesExistingSoleProprietorWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $inn                     = new Inn('540701117479');
        $persistedSoleProprietor->setInn($inn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new SoleProprietorCollection([$persistedSoleProprietor, $this->soleProprietorC]));
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->getInn());
        $this->assertSame('540701117479', (string) $persistedSoleProprietor->getInn());
        $this->assertTrue($this->soleProprietorA->getUpdatedAt() < $persistedSoleProprietor->getUpdatedAt());

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorC->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(SoleProprietorId::class, $persistedSoleProprietor->getId());
        $this->assertSame('SP003', (string) $persistedSoleProprietor->getId());
        $this->assertInstanceOf(Name::class, $persistedSoleProprietor->getName());
        $this->assertSame('ИП Сидоров Сидр Сидорович', (string) $persistedSoleProprietor->getName());
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->getInn());
        $this->assertSame('391600743661', (string) $persistedSoleProprietor->getInn());

        $this->assertSame(2, $this->getRowCount(SoleProprietor::class));
    }

    public function testItHydratesBankDetailsEmbeddable(): void
    {
        $this->repo->saveAll(new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB]));
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertNull($persistedSoleProprietor->getBankDetails());

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorB->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(BankDetails::class, $persistedSoleProprietor->getBankDetails());
        $this->assertSame('АО "АЛЬФА-БАНК"', (string) $persistedSoleProprietor->getBankDetails()->getBankName());
        $this->assertSame('044525593', (string) $persistedSoleProprietor->getBankDetails()->getBik());
        $this->assertSame('30101810200000000593', (string) $persistedSoleProprietor->getBankDetails()->getCorrespondentAccount());
        $this->assertSame('40701810401400000014', (string) $persistedSoleProprietor->getBankDetails()->getCurrentAccount());
    }

    public function testItRemovesASoleProprietor(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->repo->remove($persistedSoleProprietor);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->soleProprietorA->getId()));
        $this->assertSame(1, $this->getRowCount(SoleProprietor::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorA->getId()));
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
        $persistedSoleProprietorB = $this->repo->findById($this->soleProprietorB->getId());
        $persistedSoleProprietorC = $this->repo->findById($this->soleProprietorC->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietorB);
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietorC);
        $this->repo->removeAll(new SoleProprietorCollection([$persistedSoleProprietorB, $persistedSoleProprietorC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->soleProprietorB->getId()));
        $this->assertNull($this->repo->findById($this->soleProprietorC->getId()));
        $this->assertNotNull($this->repo->findById($this->soleProprietorA->getId()));
        $this->assertSame(3, $this->getRowCount(SoleProprietor::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorB->getId()));
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorC->getId()));
        $this->assertNull($this->getRemovedAtTimestampById(SoleProprietor::class, (string) $this->soleProprietorA->getId()));
    }

    public function testItFindsASoleProprietorById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB, $this->soleProprietorC])
        );
        $this->entityManager->clear();

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorB->getId());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertSame('SP002', (string) $persistedSoleProprietor->getId());
    }

    public function testItReturnsNullIfASoleProprietorIsNotFoundById(): void
    {
        $soleProprietor = $this->repo->findById(new SoleProprietorId('unknown_id'));
        $this->assertNull($soleProprietor);
    }
}
