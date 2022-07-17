<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCauseOfDeathRepository extends DoctrineOrmRepository implements CauseOfDeathRepository
{
    /**
     * @param EntityManagerInterface          $entityManager
     * @param CauseOfDeathRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface          $entityManager,
        CauseOfDeathRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return CauseOfDeath::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return CauseOfDeathId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return CauseOfDeathCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function doesSameNameAlreadyUsed(CauseOfDeath $causeOfDeath): bool
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
