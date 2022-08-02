<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Domain\Model\Entity;
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

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmFuneralCompanyRepository($this->entityManager);
        $this->entityA = FuneralCompanyProvider::getFuneralCompanyA();
        $this->entityB = FuneralCompanyProvider::getFuneralCompanyB();
        $this->entityC = FuneralCompanyProvider::getFuneralCompanyC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesFuneralCompanyWithSameOrganizationIdAsRemovedFuneralCompany(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var FuneralCompany $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = new FuneralCompany(new FuneralCompanyId('FC00X'), $entityToRemove->organizationId());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveFuneralCompanyWithSameOrganizationId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var FuneralCompany $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = new FuneralCompany(new FuneralCompanyId('FC00X'), $existingEntity->organizationId());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Похоронная фирма, связанная с этой организацией, уже существует.');
        $this->repo->save($newEntity);
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
