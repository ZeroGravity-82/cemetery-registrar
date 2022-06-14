<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmMemorialTreeRepository extends DoctrineOrmRepository implements MemorialTreeRepository
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
