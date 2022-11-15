<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepositoryInterface;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumRepository extends AbstractDoctrineOrmRepository implements ColumbariumRepositoryInterface
{
    protected function supportedAggregateRootClassName(): string
    {
        return Columbarium::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return ColumbariumId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return ColumbariumCollection::class;
    }

    /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    protected function assertUnique(AbstractAggregateRoot $aggregateRoot): void
    {
        /** @var Columbarium $aggregateRoot */
        if ($this->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new Exception('Колумбарий с таким наименованием уже существует.');
        }
    }

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
