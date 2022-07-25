<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmNaturalPersonRepository;
use DataFixtures\NaturalPerson\NaturalPersonProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmNaturalPersonRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = NaturalPerson::class;
    protected string $entityIdClassName         = NaturalPersonId::class;
    protected string $entityCollectionClassName = NaturalPersonCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmNaturalPersonRepository($this->entityManager);
        $this->entityA = NaturalPersonProvider::getNaturalPersonA();
        $this->entityB = NaturalPersonProvider::getNaturalPersonB();
        $this->entityC = NaturalPersonProvider::getNaturalPersonC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesNaturalPersonWithSameFullNameButWithoutBornAtAndDiedAt(): void
    {
        // Prepare the repo for testing
        /** @var NaturalPerson $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = new NaturalPerson(new NaturalPersonId('NP00X'), $existingEntity->fullName());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItSavesNaturalPersonWithSameFullNameButAnotherBornAtAndDiedAt(): void
    {
        // Prepare the repo for testing
        /** @var NaturalPerson $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new NaturalPerson(new NaturalPersonId('NP00X'), $existingEntity->fullName()))
            ->setBornAt($existingEntity->bornAt()->modify('+1 day'))
            ->setDeceasedDetails(
                new DeceasedDetails($existingEntity->deceasedDetails()->diedAt()->modify('+1 day'), null, null, null, null)
            );
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItSavesNaturalPersonWithSameFullNameAndBornAtAndDiedAtAsRemovedNaturalPerson(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var NaturalPerson $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new NaturalPerson(new NaturalPersonId('NP00X'), $entityToRemove->fullName()))
            ->setBornAt($entityToRemove->bornAt())
            ->setDeceasedDetails(
                new DeceasedDetails($entityToRemove->deceasedDetails()->diedAt(), null, null, null, null)
            );
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveNaturalPersonWithSameFullNameAndBornAt(): void
    {
        // Prepare the repo for testing
        /** @var NaturalPerson $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new NaturalPerson(new NaturalPersonId('NP00X'), $existingEntity->fullName()))
            ->setBornAt($existingEntity->bornAt());
        $this->expectExceptionForNonUniqueNaturalPerson();
        $this->repo->save($newEntity);
    }

    public function testItFailsToSaveNaturalPersonWithSameFullNameAndDiedAt(): void
    {
        // Prepare the repo for testing
        /** @var NaturalPerson $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new NaturalPerson(new NaturalPersonId('NP00X'), $existingEntity->fullName()))
            ->setDeceasedDetails(
                new DeceasedDetails($existingEntity->deceasedDetails()->diedAt(), null, null, null, null)
        );
        $this->expectExceptionForNonUniqueNaturalPerson();
        $this->repo->save($newEntity);
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var NaturalPerson $entityOne */
        /** @var NaturalPerson $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->fullName(), $entityTwo->fullName()) &&
            $this->areEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->areEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->areEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->areEqualValueObjects($entityOne->address(), $entityTwo->address()) &&
            $this->areEqualDateTimeValues($entityOne->bornAt(), $entityTwo->bornAt()) &&
            $this->areEqualValueObjects($entityOne->placeOfBirth(), $entityTwo->placeOfBirth()) &&
            $this->areEqualValueObjects($entityOne->passport(), $entityTwo->passport()) &&
            $this->areEqualValueObjects($entityOne->deceasedDetails(), $entityTwo->deceasedDetails());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newBornAt = new \DateTimeImmutable('2003-03-01');

        /** @var NaturalPerson $entityA */
        $entityA->setBornAt($newBornAt);
    }

    /**
     * @return void
     */
    private function expectExceptionForNonUniqueNaturalPerson(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Физлицо с таким ФИО и такой датой рождения или датой смерти уже существует.');
    }
}
