<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmJuristicPersonRepository extends AbstractDoctrineOrmRepository implements JuristicPersonRepositoryInterface
{
    protected function supportedAggregateRootClassName(): string
    {
        return JuristicPerson::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return JuristicPersonId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return JuristicPersonCollection::class;
    }

     /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    protected function assertUnique(AbstractAggregateRoot $aggregateRoot): void
    {
        /** @var JuristicPerson $aggregateRoot */
        if ($this->doesSameNameOrInnAlreadyUsed($aggregateRoot)) {
            throw new Exception('Юрлицо с таким наименованием или ИНН уже существует.');
        }
    }

    private function doesSameNameOrInnAlreadyUsed(JuristicPerson $juristicPerson): bool
    {
        $queryBuilder = $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('jp')
            ->select('COUNT(jp.id)')
            ->andWhere('jp.id <> :id')
            ->andWhere('jp.removedAt IS NULL')
            ->setParameter('id', $juristicPerson->id()->value());

        if ($juristicPerson->inn() !== null) {
            $queryBuilder
                ->andWhere('jp.name = :name OR jp.inn = :inn')
                ->setParameter('name', $juristicPerson->name()->value())
                ->setParameter('inn', $juristicPerson->inn()->value());
        } else {
            $queryBuilder
                ->andWhere('jp.name = :name')
                ->setParameter('name', $juristicPerson->name()->value());
        }

        return (bool) $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }
}
