<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmNaturalPersonRepository extends AbstractDoctrineOrmRepository implements NaturalPersonRepositoryInterface
{
    protected function supportedAggregateRootClassName(): string
    {
        return NaturalPerson::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return NaturalPersonId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return NaturalPersonCollection::class;
    }

    /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    protected function assertUnique(AbstractAggregateRoot $aggregateRoot): void
    {
        /** @var NaturalPerson $aggregateRoot */
        if ($this->doesSameFullNameAndBornAtOrDiedAtAlreadyUsed($aggregateRoot)) {
            throw new Exception('Физлицо с таким ФИО и такой датой рождения или датой смерти уже существует.');
        }
    }

    private function doesSameFullNameAndBornAtOrDiedAtAlreadyUsed(NaturalPerson $naturalPerson): bool
    {
        if ($naturalPerson->bornAt() === null && $naturalPerson->deceasedDetails()?->diedAt() === null) {
            return false;
        }

        $queryBuilder = $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('np')
            ->select('COUNT(np.id)')
            ->andWhere('np.id <> :id')
            ->andWhere('np.fullName = :fullName')
            ->andWhere('np.removedAt IS NULL')
            ->setParameter('id', $naturalPerson->id()->value())
            ->setParameter('fullName', $naturalPerson->fullName()->value());

        if ($naturalPerson->bornAt() !== null && $naturalPerson->deceasedDetails()?->diedAt() !== null) {
            $queryBuilder
                ->andWhere("np.bornAt = :bornAt OR JSON_EXTRACT(np.deceasedDetails, '$.diedAt') = :diedAt")
                ->setParameter('bornAt', $naturalPerson->bornAt())
                ->setParameter('diedAt', $naturalPerson->deceasedDetails()->diedAt()->format('Y-m-d'));
        }
        if ($naturalPerson->bornAt() !== null && $naturalPerson->deceasedDetails()?->diedAt() === null) {
            $queryBuilder
                ->andWhere('np.bornAt = :bornAt')
                ->setParameter('bornAt', $naturalPerson->bornAt());
        }
        if ($naturalPerson->bornAt() === null && $naturalPerson->deceasedDetails()?->diedAt() !== null) {
            $queryBuilder
                ->andWhere("JSON_EXTRACT(np.deceasedDetails, '$.diedAt') = :diedAt")
                ->setParameter('diedAt', $naturalPerson->deceasedDetails()?->diedAt()->format('Y-m-d'));
        }

        return (bool) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }
}
