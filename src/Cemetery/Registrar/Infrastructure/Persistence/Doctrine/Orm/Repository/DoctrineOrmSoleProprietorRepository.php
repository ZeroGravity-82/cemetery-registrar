<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmSoleProprietorRepository extends DoctrineOrmRepository implements SoleProprietorRepository
{
    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootClassName(): string
    {
        return SoleProprietor::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootIdClassName(): string
    {
        return SoleProprietorId::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootCollectionClassName(): string
    {
        return SoleProprietorCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var SoleProprietor $aggregateRoot */
        if ($this->doesSameNameOrInnAlreadyUsed($aggregateRoot)) {
            throw new Exception('ИП с таким наименованием или ИНН уже существует.');
        }
    }

    /**
     * @param SoleProprietor $soleProprietor
     *
     * @return bool
     */
    private function doesSameNameOrInnAlreadyUsed(SoleProprietor $soleProprietor): bool
    {
        $queryBuilder =
            $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('sp')
            ->select('COUNT(sp.id)')
            ->andWhere('sp.id <> :id')
            ->andWhere('sp.removedAt IS NULL')
            ->setParameter('id', $soleProprietor->id()->value());

        if ($soleProprietor->inn() !== null) {
            $queryBuilder
                ->andWhere('sp.name = :name OR sp.inn = :inn')
                ->setParameter('name', $soleProprietor->name()->value())
                ->setParameter('inn', $soleProprietor->inn()->value());
        } else {
            $queryBuilder
                ->andWhere('sp.name = :name')
                ->setParameter('name', $soleProprietor->name()->value());
        }

        return (bool) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }
}
