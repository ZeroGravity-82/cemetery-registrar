<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmFuneralCompanyRepository;
use DataFixtures\FuneralCompany\FuneralCompanyProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmFuneralCompanyRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = FuneralCompany::class;
    protected string $entityIdClassName         = FuneralCompanyId::class;
    protected string $entityCollectionClassName = FuneralCompanyCollection::class;

    private FuneralCompany $entityD;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmFuneralCompanyRepository($this->entityManager);
        $this->entityA = FuneralCompanyProvider::getFuneralCompanyA();
        $this->entityB = FuneralCompanyProvider::getFuneralCompanyB();
        $this->entityC = FuneralCompanyProvider::getFuneralCompanyC();
        $this->entityD = FuneralCompanyProvider::getFuneralCompanyD();
    }

    public function testItFindsFuneralCompanyByOrganizationId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new FuneralCompanyCollection(
            [$this->entityA, $this->entityB, $this->entityC, $this->entityD]
        ));
        $this->entityManager->clear();
        $this->assertSame(4, $this->getRowCount(FuneralCompany::class));

        // Testing itself
        $knownOrganizationId = new OrganizationId(new JuristicPersonId('JP001'));
        $funeralCompany      = $this->repo->findByOrganizationId($knownOrganizationId);
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->areEqualEntities($funeralCompany, $this->entityA);

        $knownOrganizationId = new OrganizationId(new SoleProprietorId('SP001'));
        $funeralCompany      = $this->repo->findByOrganizationId($knownOrganizationId);
        $this->assertInstanceOf(FuneralCompany::class, $funeralCompany);
        $this->areEqualEntities($funeralCompany, $this->entityB);

        $unknownOrganizationId = new OrganizationId(new SoleProprietorId('unknown_id'));
        $this->assertNull($this->repo->findByOrganizationId($unknownOrganizationId));
    }

    public function testItDoesNotFindRemovedFuneralCompanyByOrganizationId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new FuneralCompanyCollection(
            [$this->entityA, $this->entityB, $this->entityC, $this->entityD]
        ));
        $this->entityManager->clear();
        $this->assertSame(4, $this->getRowCount(FuneralCompany::class));

        $persistedEntityD = $this->repo->findById($this->entityD->id());
        $organizationIdD  = $persistedEntityD->organizationId();
        $this->repo->remove($persistedEntityD);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findByOrganizationId($organizationIdD));
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var FuneralCompany $entityOne */
        /** @var FuneralCompany $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->organizationId(), $entityTwo->organizationId()) &&
            $this->areEqualValueObjects($entityOne->note(), $entityTwo->note());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newNote = new FuneralCompanyNote('Примечание 1');

        /** @var FuneralCompany $entityA */
        $entityA->setNote($newNote);
    }
}