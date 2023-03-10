<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepositoryInterface;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumNicheRepository extends AbstractDoctrineOrmRepository implements ColumbariumNicheRepositoryInterface
{
    protected function supportedAggregateRootClassName(): string
    {
        return ColumbariumNiche::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return ColumbariumNicheId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return ColumbariumNicheCollection::class;
    }

    /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    protected function assertUnique(AbstractAggregateRoot $aggregateRoot): void
    {
        /** @var ColumbariumNiche $aggregateRoot */
        if ($this->doesSameNicheNumberAlreadyUsed($aggregateRoot)) {
            throw new Exception('Колумбарная ниша с таким номером в этом колумбарии уже существует.');
        }
    }

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
