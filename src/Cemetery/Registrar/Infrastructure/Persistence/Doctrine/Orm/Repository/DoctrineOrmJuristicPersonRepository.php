<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmJuristicPersonRepository extends DoctrineOrmRepository implements JuristicPersonRepository
{
    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return JuristicPerson::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return JuristicPersonId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return JuristicPersonCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var JuristicPerson $aggregateRoot */
        if ($this->doesSameNameOrInnAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException('Юрлицо с таким наименованием или ИНН уже существует.');
        }
    }

    /**
     * @param JuristicPerson $juristicPerson
     *
     * @return bool
     */
    private function doesSameNameOrInnAlreadyUsed(JuristicPerson $juristicPerson): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('jp')
            ->select('COUNT(jp.id)')
            ->andWhere('jp.id <> :id')
            ->andWhere('jp.name = :name OR jp.inn = :inn')
            ->andWhere('jp.removedAt IS NULL')
            ->setParameter('id', $juristicPerson->id()->value())
            ->setParameter('name', $juristicPerson->name()->value())
            ->setParameter('inn', $juristicPerson->inn()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
