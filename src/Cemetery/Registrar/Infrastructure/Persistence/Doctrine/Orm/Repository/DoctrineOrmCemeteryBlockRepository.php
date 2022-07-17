<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepositoryValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCemeteryBlockRepository extends DoctrineOrmRepository implements CemeteryBlockRepository
{
    /**
     * @param EntityManagerInterface           $entityManager
     * @param CemeteryBlockRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface           $entityManager,
        CemeteryBlockRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return CemeteryBlock::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return CemeteryBlockId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return CemeteryBlockCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function doesSameNameAlreadyUsed(CemeteryBlock $cemeteryBlock): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('cb')
            ->select('COUNT(cb.id)')
            ->andWhere('cb.id <> :id')
            ->andWhere('cb.name = :name')
            ->andWhere('cb.removedAt IS NULL')
            ->setParameter('id', $cemeteryBlock->id()->value())
            ->setParameter('name', $cemeteryBlock->name()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
