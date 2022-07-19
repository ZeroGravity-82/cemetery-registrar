<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepositoryValidator;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmFuneralCompanyRepository extends DoctrineOrmRepository implements FuneralCompanyRepository
{
    /**
     * @param EntityManagerInterface            $entityManager
     * @param FuneralCompanyRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface            $entityManager,
        FuneralCompanyRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return FuneralCompany::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return FuneralCompanyId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return FuneralCompanyCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function findByOrganizationId(OrganizationId $organizationId): ?FuneralCompany
    {
        return $this->entityManager
            ->getRepository(FuneralCompany::class)
            ->createQueryBuilder('fc')
            ->andWhere("JSON_EXTRACT(fc.organizationId, '$.type') = :type")
            ->andWhere("JSON_EXTRACT(fc.organizationId, '$.value') = :value")
            ->andWhere('fc.removedAt IS NULL')
            ->setParameter('type', $organizationId->idType())
            ->setParameter('value', $organizationId->id()->value())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function doesSameOrganizationIdAlreadyUsed(FuneralCompany $funeralCompany): bool
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
