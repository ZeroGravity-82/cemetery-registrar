<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepositoryValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumRepository extends DoctrineOrmRepository implements ColumbariumRepository
{
    /**
     * @param EntityManagerInterface         $entityManager
     * @param ColumbariumRepositoryValidator $repositoryValidator
     */
    public function __construct(
        EntityManagerInterface         $entityManager,
        ColumbariumRepositoryValidator $repositoryValidator,
    ) {
        parent::__construct($entityManager, $repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return Columbarium::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return ColumbariumId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return ColumbariumCollection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function doesSameNameAlreadyUsed(Columbarium $columbarium): bool
    {
        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.id <> :id')
            ->andWhere('c.name = :name')
            ->andWhere('c.removedAt IS NULL')
            ->setParameter('id', $columbarium->id()->value())
            ->setParameter('name', $columbarium->name()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
