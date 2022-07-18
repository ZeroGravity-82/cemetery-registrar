<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepositoryValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmMemorialTreeRepository extends DoctrineOrmRepository implements MemorialTreeRepository
{
    /**
     * @param EntityManagerInterface          $entityManager
     * @param MemorialTreeRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface          $entityManager,
        MemorialTreeRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return MemorialTree::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return MemorialTreeId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return MemorialTreeCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function doesSameTreeNumberAlreadyUsed(MemorialTree $memorialTree): bool
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
