<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractEntity;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmSoleProprietorRepository;
use DataFixtures\Organization\SoleProprietor\SoleProprietorProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmSoleProprietorRepositoryIntegrationTest extends AbstractDoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = SoleProprietor::class;
    protected string $entityIdClassName         = SoleProprietorId::class;
    protected string $entityCollectionClassName = SoleProprietorCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmSoleProprietorRepository($this->entityManager);
        $this->entityA = SoleProprietorProvider::getSoleProprietorA();
        $this->entityB = SoleProprietorProvider::getSoleProprietorB();
        $this->entityC = SoleProprietorProvider::getSoleProprietorC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesSoleProprietorWithSameNameAndInnAsRemovedSoleProprietor(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var SoleProprietor $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new SoleProprietor(new SoleProprietorId('SP00X'), $entityToRemove->name()))
            ->setInn($entityToRemove->inn());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveSoleProprietorWithSameNameOnly(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var SoleProprietor $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = new SoleProprietor(new SoleProprietorId('SP00X'), $existingEntity->name());
        $this->expectExceptionForNonUniqueSoleProprietor();
        $this->repo->save($newEntity);
    }

    public function testItFailsToSaveSoleProprietorWithSameInnButAnotherName(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var SoleProprietor $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = (new SoleProprietor(new SoleProprietorId('SP00X'), new Name('ИП Михеев Константин Иванович')))
            ->setInn($existingEntity->inn());
        $this->expectExceptionForNonUniqueSoleProprietor();
        $this->repo->save($newEntity);
    }

    protected function areEqualEntities(AbstractEntity $entityOne, AbstractEntity $entityTwo): bool
    {
        /** @var SoleProprietor $entityOne */
        /** @var SoleProprietor $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->areEqualValueObjects($entityOne->inn(), $entityTwo->inn()) &&
            $this->areEqualValueObjects($entityOne->ogrnip(), $entityTwo->ogrnip()) &&
            $this->areEqualValueObjects($entityOne->okpo(), $entityTwo->okpo()) &&
            $this->areEqualValueObjects($entityOne->okved(), $entityTwo->okved()) &&
            $this->areEqualValueObjects($entityOne->registrationAddress(), $entityTwo->registrationAddress()) &&
            $this->areEqualValueObjects($entityOne->actualLocationAddress(), $entityTwo->actualLocationAddress()) &&
            $this->areEqualValueObjects($entityOne->bankDetails(), $entityTwo->bankDetails()) &&
            $this->areEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->areEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->areEqualValueObjects($entityOne->fax(), $entityTwo->fax()) &&
            $this->areEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->areEqualValueObjects($entityOne->website(), $entityTwo->website());
    }

    protected function updateEntityA(AbstractEntity $entityA): void
    {
        $newInn = new Inn('391600743661');

        /** @var SoleProprietor $entityA */
        $entityA->setInn($newInn);
    }

    /**
     * @return void
     */
    private function expectExceptionForNonUniqueSoleProprietor(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ИП с таким наименованием или ИНН уже существует.');
    }
}
