<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryInterface;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCauseOfDeathRepository extends AbstractDoctrineOrmRepository implements CauseOfDeathRepositoryInterface
{
    protected function supportedAggregateRootClassName(): string
    {
        return CauseOfDeath::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return CauseOfDeathId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return CauseOfDeathCollection::class;
    }

    /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    protected function assertUnique(AbstractAggregateRoot $aggregateRoot): void
    {
        /** @var CauseOfDeath $aggregateRoot */
        if ($this->doesSameNameAlreadyUsed($aggregateRoot)) {
            throw new Exception('Причина смерти с таким наименованием уже существует.');
        }
    }

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
