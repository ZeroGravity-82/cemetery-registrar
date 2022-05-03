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
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineORMSoleProprietorRepository($this->entityManager);
        $this->truncateEntities();
    }

    public function testItSavesANewSoleProprietor(): void
    {
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertTrue($persistedSoleProprietor->id()->isEqual($this->soleProprietorA->id()));
        $this->assertTrue($persistedSoleProprietor->name()->isEqual($this->soleProprietorA->name()));
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
        $newInn = new Inn('772208786091');
        $persistedSoleProprietor->setInn($newInn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedSoleProprietor);
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->inn());
        $this->assertTrue($persistedSoleProprietor->inn()->isEqual($newInn));
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
        $newInn = new Inn('540701117479');
        $persistedSoleProprietor->setInn($newInn);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new SoleProprietorCollection([$persistedSoleProprietor, $this->soleProprietorC]));
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->inn());
        $this->assertTrue($persistedSoleProprietor->inn()->isEqual($newInn));
        $this->assertTrue($this->soleProprietorA->updatedAt() < $persistedSoleProprietor->updatedAt());

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorC->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedSoleProprietor);
        $this->assertInstanceOf(SoleProprietorId::class, $persistedSoleProprietor->id());
        $this->assertTrue($persistedSoleProprietor->id()->isEqual($this->soleProprietorC->id()));
        $this->assertInstanceOf(Name::class, $persistedSoleProprietor->name());
        $this->assertTrue($persistedSoleProprietor->name()->isEqual($this->soleProprietorC->name()));
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->inn());
        $this->assertTrue($persistedSoleProprietor->inn()->isEqual($this->soleProprietorC->inn()));

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
        $this->assertTrue($persistedSoleProprietor->bankDetails()->isEqual($this->soleProprietorB->bankDetails()));
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
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, $this->soleProprietorA->id()->value()));
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
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, $this->soleProprietorB->id()->value()));
        $this->assertNotNull($this->getRemovedAtTimestampById(SoleProprietor::class, $this->soleProprietorC->id()->value()));
        $this->assertNull($this->getRemovedAtTimestampById(SoleProprietor::class, $this->soleProprietorA->id()->value()));
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
        $this->assertTrue($persistedSoleProprietor->id()->isEqual($this->soleProprietorB->id()));
    }

    public function testItReturnsNullIfASoleProprietorIsNotFoundById(): void
    {
        $soleProprietor = $this->repo->findById(new SoleProprietorId('unknown_id'));
        $this->assertNull($soleProprietor);
    }
}
