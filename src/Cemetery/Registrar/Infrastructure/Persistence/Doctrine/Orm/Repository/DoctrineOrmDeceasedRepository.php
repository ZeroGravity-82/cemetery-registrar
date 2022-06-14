<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Deceased\Deceased;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmDeceasedRepository extends DoctrineOrmRepository implements DeceasedRepository
{
    /**
     * {@inheritdoc}
     */
    public function save(Deceased $deceased): void
    {
        $deceased->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($deceased);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(DeceasedCollection $deceaseds): void
    {
        foreach ($deceaseds as $deceased) {
            $deceased->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($deceased);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(DeceasedId $deceasedId): ?Deceased
    {
        return $this->entityManager->getRepository(Deceased::class)->findBy([
            'id'        => $deceasedId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Deceased $deceased): void
    {
        $deceased->refreshRemovedAtTimestamp();
        $this->entityManager->persist($deceased);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(DeceasedCollection $deceaseds): void
    {
        foreach ($deceaseds as $deceased) {
            $deceased->refreshRemovedAtTimestamp();
            $this->entityManager->persist($deceased);
        }
        $this->entityManager->flush();
    }
}
