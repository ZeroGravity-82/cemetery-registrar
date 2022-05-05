<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Deceased\Doctrine\ORM;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Infrastructure\Domain\Deceased\Doctrine\ORM\DeceasedRepository as DoctrineOrmDeceasedRepository;
use Cemetery\Tests\Registrar\Domain\Deceased\DeceasedProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedRepositoryIntegrationTest extends RepositoryIntegrationTest
{
    protected string $entityClassName           = Deceased::class;
    protected string $entityIdClassName         = DeceasedId::class;
    protected string $entityCollectionClassName = DeceasedCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmDeceasedRepository($this->entityManager);
        $this->entityA = DeceasedProvider::getDeceasedA();
        $this->entityB = DeceasedProvider::getDeceasedB();
        $this->entityC = DeceasedProvider::getDeceasedC();
    }

    protected function checkAreEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var Deceased $entityOne */
        /** @var Deceased $entityTwo */
        return
            $this->checkAreSameClasses($entityOne, $entityTwo) &&
            $this->checkAreEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->checkAreEqualValueObjects($entityOne->naturalPersonId(), $entityTwo->naturalPersonId()) &&
            $this->checkAreEqualDateTimeValues($entityOne->diedAt(), $entityTwo->diedAt()) &&
            $this->checkAreEqualValueObjects($entityOne->deathCertificateId(), $entityTwo->deathCertificateId()) &&
            $this->checkAreEqualValueObjects($entityOne->causeOfDeath(), $entityTwo->causeOfDeath());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newCauseOfDeath = new CauseOfDeath('Некоторая причина смерти 1');

        /** @var Deceased $entityA */
        $entityA->setCauseOfDeath($newCauseOfDeath);
    }
}
