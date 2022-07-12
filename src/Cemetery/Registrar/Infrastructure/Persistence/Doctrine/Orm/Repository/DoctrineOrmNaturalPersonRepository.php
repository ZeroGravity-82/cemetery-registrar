<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmNaturalPersonRepository extends DoctrineOrmRepository implements NaturalPersonRepository
{
    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return NaturalPerson::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return NaturalPersonId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return NaturalPersonCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCauseOfDeathId(CauseOfDeathId $causeOfDeathId): int
    {
        return (int) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('np')
            ->select('COUNT(np.id)')
            ->andWhere('np.deceased_details->>"$.causeOfDeathId" = :causeOfDeathId')
            ->andWhere('np.removedAt IS NULL')
            ->setParameter('causeOfDeathId', $causeOfDeathId->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
