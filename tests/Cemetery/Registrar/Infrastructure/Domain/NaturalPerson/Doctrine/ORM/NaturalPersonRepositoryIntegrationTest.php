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

    protected function checkAreEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var NaturalPerson $entityOne */
        /** @var NaturalPerson $entityTwo */
        return
            $this->checkAreSameClasses($entityOne, $entityTwo) &&
            $this->checkAreEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->checkAreEqualValueObjects($entityOne->fullName(), $entityTwo->fullName()) &&
            $this->checkAreEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->checkAreEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->checkAreEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->checkAreEqualValueObjects($entityOne->address(), $entityTwo->address()) &&
            $this->checkAreEqualDateTimeValues($entityOne->bornAt(), $entityTwo->bornAt()) &&
            $this->checkAreEqualValueObjects($entityOne->placeOfBirth(), $entityTwo->placeOfBirth()) &&
            $this->checkAreEqualValueObjects($entityOne->passport(), $entityTwo->passport());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newBornAt = new \DateTimeImmutable('2003-03-01');

        /** @var NaturalPerson $entityA */
        $entityA->setBornAt($newBornAt);
    }
}
