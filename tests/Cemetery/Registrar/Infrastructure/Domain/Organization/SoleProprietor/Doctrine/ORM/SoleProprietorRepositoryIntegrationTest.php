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
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorRepositoryIntegrationTest extends KernelTestCase
{
    private SoleProprietor $soleProprietorA;

    private SoleProprietor $soleProprietorB;

    private SoleProprietor $soleProprietorC;

    private EntityManagerInterface $entityManager;

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
        $this->assertSame((string) $this->soleProprietorA->getId(), (string) $persistedSoleProprietor->getId());
        $this->assertSame((string) $this->soleProprietorA->getName(), (string) $persistedSoleProprietor->getName());
        $this->assertNull($persistedSoleProprietor->getInn());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItUpdatesAnExistingSoleProprietor(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $inn                     = new Inn('772208786091');
        $persistedSoleProprietor->setInn($inn);
        $this->repo->save($persistedSoleProprietor);
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->getInn());
        $this->assertSame('772208786091', (string) $persistedSoleProprietor->getInn());
        $this->assertSame(1, $this->getRowCount());
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
        $this->assertSame(3, $this->getRowCount());
    }

    public function testItUpdatesExistingSoleProprietorWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->soleProprietorA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $inn                     = new Inn('540701117479');
        $persistedSoleProprietor->setInn($inn);
        $this->repo->saveAll(new SoleProprietorCollection([$persistedSoleProprietor, $this->soleProprietorC]));
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->getInn());
        $this->assertSame('540701117479', (string) $persistedSoleProprietor->getInn());

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorC->getId());
        $this->assertInstanceOf(SoleProprietorId::class, $persistedSoleProprietor->getId());
        $this->assertSame('SP003', (string) $persistedSoleProprietor->getId());
        $this->assertInstanceOf(Name::class, $persistedSoleProprietor->getName());
        $this->assertSame('ИП Сидоров Сидр Сидорович', (string) $persistedSoleProprietor->getName());
        $this->assertInstanceOf(Inn::class, $persistedSoleProprietor->getInn());
        $this->assertSame('391600743661', (string) $persistedSoleProprietor->getInn());

        $this->assertSame(2, $this->getRowCount());
    }

    public function testItHydratesBankDetailsEmbeddable(): void
    {
        $this->repo->saveAll(new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB]));
        $this->entityManager->clear();

        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->assertNull($persistedSoleProprietor->getBankDetails());
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorB->getId());
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
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedSoleProprietor = $this->repo->findById($this->soleProprietorA->getId());
        $this->repo->remove($persistedSoleProprietor);
        $this->assertSame(0, $this->getRowCount());
    }

    public function testItRemovesACollectionOfSoleProprietors(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(
            new SoleProprietorCollection([$this->soleProprietorA, $this->soleProprietorB, $this->soleProprietorC])
        );
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount());

        // Testing itself
        $persistedSoleProprietorB = $this->repo->findById($this->soleProprietorB->getId());
        $persistedSoleProprietorC = $this->repo->findById($this->soleProprietorC->getId());
        $this->repo->removeAll(new SoleProprietorCollection([$persistedSoleProprietorB, $persistedSoleProprietorC]));
        $this->assertSame(1, $this->getRowCount());
        $this->assertNotNull($this->repo->findById($this->soleProprietorA->getId()));
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
        $this->assertSame((string) $this->soleProprietorB->getId(), (string) $persistedSoleProprietor->getId());
    }

    public function testItReturnsNullIfASoleProprietorIsNotFoundById(): void
    {
        $soleProprietor = $this->repo->findById(new SoleProprietorId('unknown_id'));

        $this->assertNull($soleProprietor);
    }

    private function truncateEntities(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    private function getRowCount(): int
    {
        return (int) $this->entityManager
            ->getRepository(SoleProprietor::class)
            ->createQueryBuilder('sp')
            ->select('COUNT(sp.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
