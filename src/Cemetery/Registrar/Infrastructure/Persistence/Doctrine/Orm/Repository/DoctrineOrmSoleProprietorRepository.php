<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
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
            throw new \RuntimeException('ИП с таким наименованием или ИНН уже существует.');
        }
    }

    /**
     * @param SoleProprietor $juristicPerson
     *
     * @return bool
     */
    private function doesSameNameOrInnAlreadyUsed(SoleProprietor $juristicPerson): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('sp')
            ->select('COUNT(sp.id)')
            ->andWhere('sp.id <> :id')
            ->andWhere('sp.name = :name OR sp.inn = :inn')
            ->andWhere('sp.removedAt IS NULL')
            ->setParameter('id', $juristicPerson->id()->value())
            ->setParameter('name', $juristicPerson->name()->value())
            ->setParameter('inn', $juristicPerson->inn()?->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
