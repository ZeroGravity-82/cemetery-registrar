<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepositoryValidator;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepository extends DoctrineOrmRepository implements BurialRepository
{
    /**
     * @param EntityManagerInterface    $entityManager
     * @param BurialRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface    $entityManager,
        BurialRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return Burial::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return BurialId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return BurialCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function countByFuneralCompanyId(FuneralCompanyId $funeralCompanyId): int
    {
        return $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.funeralCompanyId = :funeralCompanyId')
            ->andWhere('b.removedAt IS NULL')
            ->setParameter('funeralCompanyId', $funeralCompanyId->value())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByCustomerId(CustomerId $customerId): int
    {
        return $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere("JSON_EXTRACT(b.customerId, '$.type') = :type")
            ->andWhere("JSON_EXTRACT(b.customerId, '$.value') = :value")
            ->andWhere('b.removedAt IS NULL')
            ->setParameter('type', $customerId->idType())
            ->setParameter('value', $customerId->id()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByGraveSiteId(GraveSiteId $graveSiteId): int
    {
        return $this->countByBurialPlaceId(new BurialPlaceId($graveSiteId));
    }

    /**
     * {@inheritdoc}
     */
    public function countByColumbariumNicheId(ColumbariumNicheId $columbariumNicheId): int
    {
        return $this->countByBurialPlaceId(new BurialPlaceId($columbariumNicheId));
    }

    /**
     * {@inheritdoc}
     */
    public function countByMemorialTreeId(MemorialTreeId $memorialTreeId): int
    {
        return $this->countByBurialPlaceId(new BurialPlaceId($memorialTreeId));
    }

    /**
     * @param BurialPlaceId $burialPlaceId
     *
     * @return int
     */
    private function countByBurialPlaceId(BurialPlaceId $burialPlaceId): int
    {
        return $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere("JSON_EXTRACT(b.burialPlaceId, '$.type') = :type")
            ->andWhere("JSON_EXTRACT(b.burialPlaceId, '$.value') = :value")
            ->andWhere('b.removedAt IS NULL')
            ->setParameter('type', $burialPlaceId->idType())
            ->setParameter('value', $burialPlaceId->id()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
