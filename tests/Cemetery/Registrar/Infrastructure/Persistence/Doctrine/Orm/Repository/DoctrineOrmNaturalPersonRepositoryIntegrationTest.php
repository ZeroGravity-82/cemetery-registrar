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

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(NaturalPerson::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(NaturalPersonId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(NaturalPersonCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    public function testItSaveNaturalWithSameFullNameButWithoutBornAtAndDiedAt(): void
    {

    }

    public function testItSaveNaturalWithSameFullNameButAnotherBornAtAndDiedAt(): void
    {

    }

    public function testIfFailsToSaveNaturalPersonWithSameFullNameAndBornAt(): void
    {
        /** @var NaturalPerson $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        $newEntity = (new NaturalPerson(new NaturalPersonId('NP00X'), $existingEntity->fullName()))
            ->setBornAt($existingEntity->bornAt());

        $this->expectExceptionForNonUniqueNaturalPerson();
        $this->repo->save($newEntity);
    }

    public function testIfFailsToSaveNaturalPersonWithSameFullNameAndDiedAt(): void
    {
        /** @var NaturalPerson $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        $newEntity = (new NaturalPerson(new NaturalPersonId('NP00X'), $existingEntity->fullName()))
            ->setDeceasedDetails(new DeceasedDetails());

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
