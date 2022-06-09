<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\Orm;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Infrastructure\Domain\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmGraveSiteRepository extends Repository implements GraveSiteRepository
{
    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function save(GraveSite $graveSite): void
    {
        $graveSite->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($graveSite);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function saveAll(GraveSiteCollection $graveSites): void
    {
        foreach ($graveSites as $graveSite) {
            $graveSite->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($graveSite);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(GraveSiteId $graveSiteId): ?GraveSite
    {
        return $this->entityManager->getRepository(GraveSite::class)->findBy([
            'id'        => $graveSiteId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(GraveSite $graveSite): void
    {
        $graveSite->refreshRemovedAtTimestamp();
        $this->entityManager->persist($graveSite);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(GraveSiteCollection $graveSites): void
    {
        foreach ($graveSites as $graveSite) {
            $graveSite->refreshRemovedAtTimestamp();
            $this->entityManager->persist($graveSite);
        }
        $this->entityManager->flush();
    }
}
