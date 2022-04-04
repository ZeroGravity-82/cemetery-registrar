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
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private FuneralCompany                      $funeralCompanyA;
    private FuneralCompany                      $funeralCompanyB;
    private FuneralCompany                      $funeralCompanyC;
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
        $this->assertSame(1, $this->getRowCount(FuneralCompany::class));
        $this->assertSame(
            $this->funeralCompanyA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedFuneralCompany->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->funeralCompanyA->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            $persistedFuneralCompany->getUpdatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedFuneralCompany->getRemovedAt());
    }

    public function testItUpdatesAnExistingFuneralCompany(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->funeralCompanyA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(FuneralCompany::class));

        // Testing itself
        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $note                    = 'Некоторый комментарий';
        $persistedFuneralCompany->setNote($note);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedFuneralCompany);
        $this->entityManager->clear();

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->assertSame('Некоторый комментарий', $persistedFuneralCompany->getNote());
        $this->assertSame(1, $this->getRowCount(FuneralCompany::class));
        $this->assertSame(
            $this->funeralCompanyA->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $persistedFuneralCompany->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->funeralCompanyA->getUpdatedAt() < $persistedFuneralCompany->getUpdatedAt());
        $this->assertNull($persistedFuneralCompany->getRemovedAt());
    }

    public function testItSavesACollectionOfNewFuneralCompanies(): void
    {
        $this->repo->saveAll(new FuneralCompanyCollection([$this->funeralCompanyA, $this->funeralCompanyB, $this->funeralCompanyC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->funeralCompanyA->getId()));
        $this->assertNotNull($this->repo->findById($this->funeralCompanyB->getId()));
        $this->assertNotNull($this->repo->findById($this->funeralCompanyC->getId()));
        $this->assertSame(3, $this->getRowCount(FuneralCompany::class));
    }

    public function testItUpdatesExistingFuneralCompanyWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new FuneralCompanyCollection([$this->funeralCompanyA, $this->funeralCompanyB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(FuneralCompany::class));

        // Testing itself
        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $note                    = 'Новый комментарий';
        $persistedFuneralCompany->setNote($note);
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new FuneralCompanyCollection([$persistedFuneralCompany, $this->funeralCompanyC]));
        $this->entityManager->clear();

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->assertSame('Новый комментарий', $persistedFuneralCompany->getNote());
        $this->assertTrue($this->funeralCompanyA->getUpdatedAt() < $persistedFuneralCompany->getUpdatedAt());

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyB->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->assertInstanceOf(FuneralCompanyId::class, $persistedFuneralCompany->getId());
        $this->assertSame('FC002', (string) $persistedFuneralCompany->getId());
        $this->assertSame('888', $persistedFuneralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::SOLE_PROPRIETOR, (string) $persistedFuneralCompany->getOrganizationId()->getType());
        $this->assertSame('Некоторый комментарий', $persistedFuneralCompany->getNote());

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyC->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->assertInstanceOf(FuneralCompanyId::class, $persistedFuneralCompany->getId());
        $this->assertSame('FC003', (string) $persistedFuneralCompany->getId());
        $this->assertSame('999', $persistedFuneralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::SOLE_PROPRIETOR, (string) $persistedFuneralCompany->getOrganizationId()->getType());
        $this->assertSame('Другой комментарий', $persistedFuneralCompany->getNote());

        $this->assertSame(3, $this->getRowCount(FuneralCompany::class));
    }

    public function testItHydratesOrganizationIdEmbeddable(): void
    {
        $this->repo->save($this->funeralCompanyA);
        $this->entityManager->clear();

        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->assertInstanceOf(OrganizationId::class, $persistedFuneralCompany->getOrganizationId());
        $this->assertSame('777', $persistedFuneralCompany->getOrganizationId()->getValue());
        $this->assertSame(OrganizationType::JURISTIC_PERSON, (string) $persistedFuneralCompany->getOrganizationId()->getType());
    }

    public function testItRemovesAFuneralCompany(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->funeralCompanyA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(FuneralCompany::class));

        // Testing itself
        $persistedFuneralCompany = $this->repo->findById($this->funeralCompanyA->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompany);
        $this->repo->remove($persistedFuneralCompany);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->funeralCompanyA->getId()));
        $this->assertSame(1, $this->getRowCount(FuneralCompany::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(FuneralCompany::class, (string) $this->funeralCompanyA->getId()));
    }

    public function testItRemovesACollectionOfFuneralCompanies(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new FuneralCompanyCollection([$this->funeralCompanyA, $this->funeralCompanyB, $this->funeralCompanyC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(FuneralCompany::class));

        // Testing itself
        $persistedFuneralCompanyB = $this->repo->findById($this->funeralCompanyB->getId());
        $persistedFuneralCompanyC = $this->repo->findById($this->funeralCompanyC->getId());
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompanyB);
        $this->assertInstanceOf(FuneralCompany::class, $persistedFuneralCompanyC);
        $this->repo->removeAll(new FuneralCompanyCollection([$persistedFuneralCompanyB, $persistedFuneralCompanyC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->funeralCompanyB->getId()));
        $this->assertNull($this->repo->findById($this->funeralCompanyC->getId()));
        $this->assertNotNull($this->repo->findById($this->funeralCompanyA->getId()));
        $this->assertSame(3, $this->getRowCount(FuneralCompany::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(FuneralCompany::class, (string) $this->funeralCompanyB->getId()));
        $this->assertNotNull($this->getRemovedAtTimestampById(FuneralCompany::class, (string) $this->funeralCompanyC->getId()));
        $this->assertNull($this->getRemovedAtTimestampById(FuneralCompany::class, (string) $this->funeralCompanyA->getId()));
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
}
