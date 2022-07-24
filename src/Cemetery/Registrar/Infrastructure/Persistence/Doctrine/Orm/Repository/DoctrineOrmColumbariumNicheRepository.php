<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumNicheRepository extends DoctrineOrmRepository implements ColumbariumNicheRepository
{
    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return ColumbariumNiche::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return ColumbariumNicheId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return ColumbariumNicheCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var ColumbariumNiche $aggregateRoot */
        if ($this->doesSameNicheNumberAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException('Колумбарная ниша с таким номером уже существует в этом колумбарии.');
        }
    }

    /**
     * @param ColumbariumNiche $columbariumNiche
     *
     * @return bool
     */
    private function doesSameNicheNumberAlreadyUsed(ColumbariumNiche $columbariumNiche): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('cn')
            ->select('COUNT(cn.id)')
            ->andWhere('cn.id <> :id')
            ->andWhere('cn.columbariumId = :columbariumId')
            ->andWhere('cn.nicheNumber = :nicheNumber')
            ->andWhere('cn.removedAt IS NULL')
            ->setParameter('id', $columbariumNiche->id()->value())
            ->setParameter('columbariumId', $columbariumNiche->columbariumId()->value())
            ->setParameter('nicheNumber', $columbariumNiche->nicheNumber()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
