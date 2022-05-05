<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM\NaturalPersonRepository as DoctrineORMNaturalPersonRepository;
use Cemetery\Tests\Registrar\Domain\NaturalPerson\NaturalPersonProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRepositoryIntegrationTest extends RepositoryIntegrationTest
{
    protected string $entityClassName           = NaturalPerson::class;
    protected string $entityIdClassName         = NaturalPersonId::class;
    protected string $entityCollectionClassName = NaturalPersonCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineORMNaturalPersonRepository($this->entityManager);
        $this->entityA = NaturalPersonProvider::getNaturalPersonA();
        $this->entityB = NaturalPersonProvider::getNaturalPersonB();
        $this->entityC = NaturalPersonProvider::getNaturalPersonC();
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
            $this->areEqualValueObjects($entityOne->passport(), $entityTwo->passport());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newBornAt = new \DateTimeImmutable('2003-03-01');

        /** @var NaturalPerson $entityA */
        $entityA->setBornAt($newBornAt);
    }
}
