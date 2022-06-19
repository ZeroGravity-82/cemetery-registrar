<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCauseOfDeathRepository extends DoctrineOrmRepository implements CauseOfDeathRepository
{
    /**
     * {@inheritdoc}
     */
    public function save(CauseOfDeath $causeOfDeath): void
    {
        $causeOfDeath->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($causeOfDeath);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(CauseOfDeathCollection $causesOfDeath): void
    {
        foreach ($causesOfDeath as $causeOfDeath) {
            $causeOfDeath->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($causeOfDeath);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(CauseOfDeathId $causeOfDeathId): ?CauseOfDeath
    {
        return $this->entityManager->getRepository(CauseOfDeath::class)->findBy([
            'id'        => $causeOfDeathId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(CauseOfDeath $causeOfDeath): void
    {
        $causeOfDeath->refreshRemovedAtTimestamp();
        $this->entityManager->persist($causeOfDeath);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(CauseOfDeathCollection $causesOfDeath): void
    {
        foreach ($causesOfDeath as $causeOfDeath) {
            $causeOfDeath->refreshRemovedAtTimestamp();
            $this->entityManager->persist($causeOfDeath);
        }
        $this->entityManager->flush();
    }
}
