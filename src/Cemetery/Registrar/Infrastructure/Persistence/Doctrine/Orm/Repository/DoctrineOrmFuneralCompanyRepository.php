<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmFuneralCompanyRepository extends DoctrineOrmRepository implements FuneralCompanyRepository
{
    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootClassName(): string
    {
        return FuneralCompany::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootIdClassName(): string
    {
        return FuneralCompanyId::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedAggregateRootCollectionClassName(): string
    {
        return FuneralCompanyCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var FuneralCompany $aggregateRoot */
        if ($this->doesSameOrganizationIdAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException('Похоронная фирма, связанная с этой организацией, уже существует.');
        }
    }

    /**
     * @param FuneralCompany $funeralCompany
     *
     * @return bool
     */
    private function doesSameOrganizationIdAlreadyUsed(FuneralCompany $funeralCompany): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('fc')
            ->select('COUNT(fc.id)')
            ->andWhere('fc.id <> :id')
            ->andWhere("JSON_EXTRACT(fc.organizationId, '$.type') = :type")
            ->andWhere("JSON_EXTRACT(fc.organizationId, '$.value') = :value")
            ->andWhere('fc.removedAt IS NULL')
            ->setParameter('id', $funeralCompany->id()->value())
            ->setParameter('type', $funeralCompany->organizationId()->idType())
            ->setParameter('value', $funeralCompany->organizationId()->id()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
