<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCauseOfDeathRepository extends DoctrineOrmRepository implements CauseOfDeathRepository
{
    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootClassName(): string
    {
        return CauseOfDeath::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootIdClassName(): string
    {
        return CauseOfDeathId::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootCollectionClassName(): string
    {
        return CauseOfDeathCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var CauseOfDeath $aggregateRoot */
        if ($this->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException(\sprintf(
                'Причина смерти "%s" уже существует.',
                $aggregateRoot->name()->value(),
            ));
        }
    }

    /**
     * @param CauseOfDeath $causeOfDeath
     *
     * @return bool
     */
    private function doesSameNameAlreadyUsed(CauseOfDeath $causeOfDeath): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('cd')
            ->select('COUNT(cd.id)')
            ->andWhere('cd.id <> :id')
            ->andWhere('cd.name = :name')
            ->andWhere('cd.removedAt IS NULL')
            ->setParameter('id', $causeOfDeath->id()->value())
            ->setParameter('name', $causeOfDeath->name()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
