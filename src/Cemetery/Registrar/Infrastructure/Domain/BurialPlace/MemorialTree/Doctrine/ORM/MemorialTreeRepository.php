<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\BurialPlace\MemorialTree\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeRepository as MemorialTreeRepositoryInterface;
use Cemetery\Registrar\Infrastructure\Domain\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class MemorialTreeRepository extends Repository implements MemorialTreeRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function save(MemorialTree $memorialTree): void
    {
        $memorialTree->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($memorialTree);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @todo Re-throw exception about integrity constraint violation
     */
    public function saveAll(MemorialTreeCollection $memorialTrees): void
    {
        foreach ($memorialTrees as $memorialTree) {
            $memorialTree->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($memorialTree);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(MemorialTreeId $memorialTreeId): ?MemorialTree
    {
        return $this->entityManager->getRepository(MemorialTree::class)->findBy([
            'id'        => $memorialTreeId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(MemorialTree $memorialTree): void
    {
        $memorialTree->refreshRemovedAtTimestamp();
        $this->entityManager->persist($memorialTree);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(MemorialTreeCollection $memorialTrees): void
    {
        foreach ($memorialTrees as $memorialTree) {
            $memorialTree->refreshRemovedAtTimestamp();
            $this->entityManager->persist($memorialTree);
        }
        $this->entityManager->flush();
    }
}
