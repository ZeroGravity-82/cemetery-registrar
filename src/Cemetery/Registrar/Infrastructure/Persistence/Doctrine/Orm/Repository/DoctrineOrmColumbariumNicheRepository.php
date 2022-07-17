<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheRepositoryValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumNicheRepository extends DoctrineOrmRepository implements ColumbariumNicheRepository
{
    /**
     * @param EntityManagerInterface              $entityManager
     * @param ColumbariumNicheRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface              $entityManager,
        ColumbariumNicheRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return ColumbariumNiche::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return ColumbariumNicheId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return ColumbariumNicheCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function countByColumbariumId(ColumbariumId $columbariumId): int
    {
        return $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('cn')
            ->select('COUNT(cn.id)')
            ->andWhere('cn.columbariumId = :columbariumId')
            ->andWhere('cn.removedAt IS NULL')
            ->setParameter('columbariumId', $columbariumId->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
