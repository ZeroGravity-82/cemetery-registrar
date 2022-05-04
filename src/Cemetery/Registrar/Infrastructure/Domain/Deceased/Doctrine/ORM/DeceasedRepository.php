<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\Deceased\Doctrine\ORM;

use Cemetery\Registrar\Domain\Deceased\Deceased;
use Cemetery\Registrar\Domain\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Deceased\DeceasedRepositoryInterface;
use Cemetery\Registrar\Infrastructure\Domain\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeceasedRepository extends Repository implements DeceasedRepositoryInterface
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
            'id'        => (string) $deceasedId,
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
