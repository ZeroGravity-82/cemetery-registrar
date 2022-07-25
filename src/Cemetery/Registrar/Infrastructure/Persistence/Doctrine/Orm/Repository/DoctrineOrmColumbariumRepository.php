<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumRepository extends DoctrineOrmRepository implements ColumbariumRepository
{
    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootClassName(): string
    {
        return Columbarium::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootIdClassName(): string
    {
        return ColumbariumId::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootCollectionClassName(): string
    {
        return ColumbariumCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var Columbarium $aggregateRoot */
        if ($this->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException('Колумбарий с таким наименованием уже существует.');
        }
    }

    /**
     * @param Columbarium $columbarium
     *
     * @return bool
     */
    private function doesSameNameAlreadyUsed(Columbarium $columbarium): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.id <> :id')
            ->andWhere('c.name = :name')
            ->andWhere('c.removedAt IS NULL')
            ->setParameter('id', $columbarium->id()->value())
            ->setParameter('name', $columbarium->name()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
