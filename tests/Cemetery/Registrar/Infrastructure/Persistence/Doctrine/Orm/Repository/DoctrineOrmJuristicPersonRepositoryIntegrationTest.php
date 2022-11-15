<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractEntity;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmJuristicPersonRepository;
use DataFixtures\Organization\JuristicPerson\JuristicPersonProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmJuristicPersonRepositoryIntegrationTest extends AbstractDoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = JuristicPerson::class;
    protected string $entityIdClassName         = JuristicPersonId::class;
    protected string $entityCollectionClassName = JuristicPersonCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmJuristicPersonRepository($this->entityManager);
        $this->entityA = JuristicPersonProvider::getJuristicPersonA();
        $this->entityB = JuristicPersonProvider::getJuristicPersonB();
        $this->entityC = JuristicPersonProvider::getJuristicPersonC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesJuristicPersonWithSameNameAndInnAsRemovedJuristicPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var JuristicPerson $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new JuristicPerson(new JuristicPersonId('JP00X'), $entityToRemove->name()))
            ->setInn($entityToRemove->inn());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveJuristicPersonWithSameNameOnly(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var JuristicPerson $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = new JuristicPerson(new JuristicPersonId('JP00X'), $existingEntity->name());
        $this->expectExceptionForNonUniqueJuristicPerson();
        $this->repo->save($newEntity);
    }

    public function testItFailsToSaveJuristicPersonWithSameInnButAnotherName(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var JuristicPerson $existingEntity */
        $existingEntity = $this->entityB;

        // Testing itself
        $newEntity = (new JuristicPerson(new JuristicPersonId('JP00X'), new Name('ООО "Авангард"')))
            ->setInn($existingEntity->inn());
        $this->expectExceptionForNonUniqueJuristicPerson();
        $this->repo->save($newEntity);
    }

    protected function areEqualEntities(AbstractEntity $entityOne, AbstractEntity $entityTwo): bool
    {
        /** @var JuristicPerson $entityOne */
        /** @var JuristicPerson $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->areEqualValueObjects($entityOne->inn(), $entityTwo->inn()) &&
            $this->areEqualValueObjects($entityOne->kpp(), $entityTwo->kpp()) &&
            $this->areEqualValueObjects($entityOne->ogrn(), $entityTwo->ogrn()) &&
            $this->areEqualValueObjects($entityOne->okpo(), $entityTwo->okpo()) &&
            $this->areEqualValueObjects($entityOne->okved(), $entityTwo->okved()) &&
            $this->areEqualValueObjects($entityOne->legalAddress(), $entityTwo->legalAddress()) &&
            $this->areEqualValueObjects($entityOne->postalAddress(), $entityTwo->postalAddress()) &&
            $this->areEqualValueObjects($entityOne->bankDetails(), $entityTwo->bankDetails()) &&
            $this->areEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->areEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->areEqualValueObjects($entityOne->fax(), $entityTwo->fax()) &&
            $this->areEqualValueObjects($entityOne->generalDirector(), $entityTwo->generalDirector()) &&
            $this->areEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->areEqualValueObjects($entityOne->website(), $entityTwo->website());
    }

    protected function updateEntityA(AbstractEntity $entityA): void
    {
        $newInn = new Inn('7728168971');

        /** @var JuristicPerson $entityA */
        $entityA->setInn($newInn);
    }

    /**
     * @return void
     */
    private function expectExceptionForNonUniqueJuristicPerson(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Юрлицо с таким наименованием или ИНН уже существует.');
    }
}
