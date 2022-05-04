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

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var Deceased $entityOne */
        /** @var Deceased $entityTwo */
        $isSameClass = $entityOne instanceof Deceased && $entityTwo instanceof Deceased;

        // Mandatory properties
        $isSameId              = $entityOne->id()->isEqual($entityTwo->id());
        $isSameNaturalPersonId = $entityOne->naturalPersonId()->isEqual($entityTwo->naturalPersonId());
        $isSameDiedAt          = $this->isEqualDateTimeValues($entityOne->diedAt(), $entityTwo->diedAt());

        // Optional properties
        $isSameDeathCertificateId = $entityOne->deathCertificateId() !== null && $entityTwo->deathCertificateId() !== null
            ? $entityOne->deathCertificateId()->isEqual($entityTwo->deathCertificateId())
            : $entityOne->deathCertificateId() === null && $entityTwo->deathCertificateId() === null;
        $isSameCauseOfDeath = $entityOne->causeOfDeath() !== null && $entityTwo->causeOfDeath() !== null
            ? $entityOne->causeOfDeath()->isEqual($entityTwo->causeOfDeath())
            : $entityOne->causeOfDeath() === null && $entityTwo->causeOfDeath() === null;

        return
            $isSameClass && $isSameId && $isSameNaturalPersonId && $isSameDiedAt && $isSameDeathCertificateId &&
            $isSameCauseOfDeath;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newDeathCertificateId = new DeathCertificateId('DC001');
        $newCauseOfDeath       = new CauseOfDeath('Некоторая причина смерти 1');

        /** @var Deceased $entityA */
        $entityA->setDeathCertificateId($newDeathCertificateId);
        $entityA->setCauseOfDeath($newCauseOfDeath);
    }
}
