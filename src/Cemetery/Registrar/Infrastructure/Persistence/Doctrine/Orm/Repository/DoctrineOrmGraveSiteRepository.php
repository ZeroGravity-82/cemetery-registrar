<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmGraveSiteRepository extends DoctrineOrmRepository implements GraveSiteRepository
{
    /**
     * @param EntityManagerInterface       $entityManager
     * @param GraveSiteRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface       $entityManager,
        GraveSiteRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return GraveSite::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return GraveSiteId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return GraveSiteCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCemeteryBlockId(CemeteryBlockId $cemeteryBlockId): int
    {
        return $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('gs')
            ->select('COUNT(gs.id)')
            ->andWhere('gs.cemeteryBlockId = :cemeteryBlockId')
            ->andWhere('gs.removedAt IS NULL')
            ->setParameter('cemeteryBlockId', $cemeteryBlockId->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
