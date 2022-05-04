<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Domain\NaturalPerson\Doctrine\ORM\NaturalPersonRepository as DoctrineORMNaturalPersonRepository;
use Cemetery\Tests\Registrar\Domain\NaturalPerson\NaturalPersonProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
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

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var NaturalPerson $entityOne */
        /** @var NaturalPerson $entityTwo */
        $isSameClass = $entityOne instanceof NaturalPerson && $entityTwo instanceof NaturalPerson;

        // Mandatory properties
        $isSameId       = $entityOne->id()->isEqual($entityTwo->id());
        $isSameFullName = $entityOne->fullName()->isEqual($entityTwo->fullName());

        // Optional properties
        $isSamePhone = $entityOne->phone() !== null && $entityTwo->phone() !== null
            ? $entityOne->phone()->isEqual($entityTwo->phone())
            : $entityOne->phone() === null && $entityTwo->phone() === null;
        $isSamePhoneAdditional = $entityOne->phoneAdditional() !== null && $entityTwo->phoneAdditional() !== null
            ? $entityOne->phoneAdditional()->isEqual($entityTwo->phoneAdditional())
            : $entityOne->phoneAdditional() === null && $entityTwo->phoneAdditional() === null;
        $isSameEmail = $entityOne->email() !== null && $entityTwo->email() !== null
            ? $entityOne->email()->isEqual($entityTwo->email())
            : $entityOne->email() === null && $entityTwo->email() === null;
        $isSameAddress = $entityOne->address() !== null && $entityTwo->address() !== null
            ? $entityOne->address()->isEqual($entityTwo->address())
            : $entityOne->address() === null && $entityTwo->address() === null;
        $isSameBornAt = $entityOne->bornAt() !== null && $entityTwo->bornAt() !== null
            ? $this->isEqualDateTimeValues($entityOne->bornAt(), $entityTwo->bornAt())
            : $entityOne->bornAt() === null && $entityTwo->bornAt() === null;
        $isSamePlaceOfBirth = $entityOne->placeOfBirth() !== null && $entityTwo->placeOfBirth() !== null
            ? $entityOne->placeOfBirth()->isEqual($entityTwo->placeOfBirth())
            : $entityOne->placeOfBirth() === null && $entityTwo->placeOfBirth() === null;
        $isSamePassport = $entityOne->passport() !== null && $entityTwo->passport() !== null
            ? $entityOne->passport()->isEqual($entityTwo->passport())
            : $entityOne->passport() === null && $entityTwo->passport() === null;

        return
            $isSameClass && $isSameId && $isSameFullName && $isSamePhone && $isSamePhoneAdditional && $isSameEmail &&
            $isSameAddress && $isSameBornAt && $isSamePlaceOfBirth && $isSamePassport;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newBornAt = new \DateTimeImmutable('2003-03-01');

        /** @var NaturalPerson $entityA */
        $entityA->setBornAt($newBornAt);
    }
}
