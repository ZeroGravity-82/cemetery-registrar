<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmMemorialTreeRepository extends DoctrineOrmRepository implements MemorialTreeRepository
{
    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootClassName(): string
    {
        return MemorialTree::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootIdClassName(): string
    {
        return MemorialTreeId::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootCollectionClassName(): string
    {
        return MemorialTreeCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var MemorialTree $aggregateRoot */
        if ($this->doesSameTreeNumberAlreadyUsed($aggregateRoot)) {
            throw new Exception('Памятное дерево с таким номером уже существует.');
        }
    }

    /**
     * @param MemorialTree $memorialTree
     *
     * @return bool
     */
    private function doesSameTreeNumberAlreadyUsed(MemorialTree $memorialTree): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('mt')
            ->select('COUNT(mt.id)')
            ->andWhere('mt.id <> :id')
            ->andWhere('mt.treeNumber = :treeNumber')
            ->andWhere('mt.removedAt IS NULL')
            ->setParameter('id', $memorialTree->id()->value())
            ->setParameter('treeNumber', $memorialTree->treeNumber()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
