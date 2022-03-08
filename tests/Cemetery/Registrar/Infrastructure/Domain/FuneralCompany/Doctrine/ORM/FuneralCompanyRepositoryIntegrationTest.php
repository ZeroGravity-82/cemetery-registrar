<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\FuneralCompany\Doctrine\ORM;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Organization\OrganizationType;
use Cemetery\Registrar\Infrastructure\Domain\FuneralCompany\Doctrine\ORM\FuneralCompanyRepository as DoctrineORMFuneralCompanyRepository;
use Cemetery\Tests\Registrar\Domain\FuneralCompany\FuneralCompanyProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyRepositoryIntegrationTest extends KernelTestCase
{
    private FuneralCompany                      $funeralCompanyA;
    private FuneralCompany                      $funeralCompanyB;
    private FuneralCompany                      $funeralCompanyC;
    private EntityManagerInterface              $entityManager;
    private DoctrineORMFuneralCompanyRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->funeralCompanyA = FuneralCompanyProvider::getFuneralCompanyA();
        $this->funeralCompanyB = FuneralCompanyProvider::getFuneralCompanyB();
        $this->funeralCompanyC = FuneralCompanyProvider::getFuneralCompanyC();
        $this->entityManager   = $container->get(EntityManagerInterface::class);
        $this->repo            = new DoctrineORMFuneralCompanyRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewFuneralCompany(): void
    {
        $this->repo->save($this->funeralCompanyA);
        $this->entityManager->clear();

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->assertSame('FC001', (string) $persistedFuneralCompany->getId());
        $this->assertSame('777', $persistedFuneralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::JURISTIC_PERSON, (string) $persistedFuneralCompany->getOrganizationId()->getType());
        $this->assertNull($persistedFuneralCompany->getNote());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItUpdatesAnExistingFuneralCompany(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->funeralCompanyA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $note                    = 'Некоторый комментарий';
        $persistedFuneralCompany->setNote($note);
        $this->repo->save($persistedFuneralCompany);
        $this->entityManager->clear();

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertSame('Некоторый комментарий', $persistedFuneralCompany->getNote());
        $this->assertSame(1, $this->getRowCount());
    }

    public function testItSavesACollectionOfNewFuneralCompanies(): void
    {
        $this->repo->saveAll(new FuneralCompanyCollection([$this->funeralCompanyA, $this->funeralCompanyB, $this->funeralCompanyC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->funeralCompanyA->getId()));
        $this->assertNotNull($this->repo->findById($this->funeralCompanyB->getId()));
        $this->assertNotNull($this->repo->findById($this->funeralCompanyC->getId()));
        $this->assertSame(3, $this->getRowCount());
    }

    public function testItUpdatesExistingFuneralCompanyWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new FuneralCompanyCollection([$this->funeralCompanyA, $this->funeralCompanyB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount());

        // Testing itself
        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $note                    = 'Новый комментарий';
        $persistedFuneralCompany->setNote($note);
        $this->repo->saveAll(new FuneralCompanyCollection([$persistedFuneralCompany, $this->funeralCompanyC]));
        $this->entityManager->clear();

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertSame('Новый комментарий', $persistedFuneralCompany->getNote());

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyB->getId());
        $this->assertInstanceOf(FuneralCompanyId::class, $persistedFuneralCompany->getId());
        $this->assertSame('FC002', (string) $persistedFuneralCompany->getId());
        $this->assertSame('888', $persistedFuneralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::SOLE_PROPRIETOR, (string) $persistedFuneralCompany->getOrganizationId()->getType());
        $this->assertSame('Некоторый комментарий', $persistedFuneralCompany->getNote());

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyC->getId());
        $this->assertInstanceOf(FuneralCompanyId::class, $persistedFuneralCompany->getId());
        $this->assertSame('FC003', (string) $persistedFuneralCompany->getId());
        $this->assertSame('999', $persistedFuneralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::SOLE_PROPRIETOR, (string) $persistedFuneralCompany->getOrganizationId()->getType());
        $this->assertSame('Другой комментарий', $persistedFuneralCompany->getNote());

        $this->assertSame(3, $this->getRowCount());
    }

    public function testItHydratesOrganizationIdEmbeddable(): void
    {
        $this->repo->save($this->funeralCompanyA);
        $this->entityManager->clear();

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(OrganizationId::class, $persistedFuneralCompany->getOrganizationId());
        $this->assertSame('777', $persistedFuneralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::JURISTIC_PERSON, (string) $persistedFuneralCompany->getOrganizationId()->getType());
    }

    public function testItRemovesAFuneralCompany(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->funeralCompanyA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount());

        // Testing itself
        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->repo->remove($persistedFuneralCompany);
        $this->assertSame(0, $this->getRowCount());
    }

    public function testItRemovesACollectionOfFuneralCompanies(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new FuneralCompanyCollection([$this->funeralCompanyA, $this->funeralCompanyB, $this->funeralCompanyC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount());

        // Testing itself
        $persistedFuneralCompanyB = $this->repo->findById($this->funeralCompanyB->getId());
        $persistedFuneralCompanyC = $this->repo->findById($this->funeralCompanyC->getId());
        $this->repo->removeAll(new FuneralCompanyCollection([$persistedFuneralCompanyB, $persistedFuneralCompanyC]));
        $this->assertSame(1, $this->getRowCount());
        $this->assertNotNull($this->repo->findById($this->funeralCompanyA->getId()));
    }

    public function testItFindsAFuneralCompanyById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new FuneralCompanyCollection([$this->funeralCompanyA, $this->funeralCompanyB, $this->funeralCompanyC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyB->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->assertSame('FC002', (string) $persistedFuneralCompany->getId());
    }

    public function testItReturnsNullIfAFuneralCompanyIsNotFoundById(): void
    {
        $funeralCompany = $this->repo->findById(new FuneralCompanyId('unknown_id'));

        $this->assertNull($funeralCompany);
    }

    private function truncateEntities(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    private function getRowCount(): int
    {
        return (int) $this->entityManager
            ->getRepository(FuneralCompany::class)
            ->createQueryBuilder('fc')
            ->select('COUNT(fc.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
