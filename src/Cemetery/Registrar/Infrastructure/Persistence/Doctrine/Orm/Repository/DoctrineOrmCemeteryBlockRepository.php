<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCemeteryBlockRepository extends DoctrineOrmRepository implements CemeteryBlockRepository
{
    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootClassName(): string
    {
        return CemeteryBlock::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootIdClassName(): string
    {
        return CemeteryBlockId::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootCollectionClassName(): string
    {
        return CemeteryBlockCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var CemeteryBlock $aggregateRoot */
        if ($this->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException('Квартал с таким наименованием уже существует.');
        }
    }

    /**
     * @param CemeteryBlock $cemeteryBlock
     *
     * @return bool
     */
    private function doesSameNameAlreadyUsed(CemeteryBlock $cemeteryBlock): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('cb')
            ->select('COUNT(cb.id)')
            ->andWhere('cb.id <> :id')
            ->andWhere('cb.name = :name')
            ->andWhere('cb.removedAt IS NULL')
            ->setParameter('id', $cemeteryBlock->id()->value())
            ->setParameter('name', $cemeteryBlock->name()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
