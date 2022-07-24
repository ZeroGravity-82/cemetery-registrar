<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmGraveSiteRepository extends DoctrineOrmRepository implements GraveSiteRepository
{
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
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        /** @var GraveSite $aggregateRoot */
        if ($this->doesSameNicheRowAndPositionAlreadyUsed($aggregateRoot)) {
            throw new \RuntimeException('Участок с рядом и местом уже существует в этом квартале.');
        }
    }

    /**
     * @param GraveSite $graveSite
     *
     * @return bool
     */
    private function doesSameNicheRowAndPositionAlreadyUsed(GraveSite $graveSite): bool
    {
        if ($graveSite->positionInRow() === null) {
            return false;
        }

        return (bool) $this->entityManager
            ->getRepository($this->supportedAggregateRootClassName())
            ->createQueryBuilder('gs')
            ->select('COUNT(gs.id)')
            ->andWhere('gs.id <> :id')
            ->andWhere('gs.rowInBlock = :rowInBlock')
            ->andWhere('gs.positionInRow = :positionInRow')
            ->andWhere('gs.removedAt IS NULL')
            ->setParameter('id', $graveSite->id()->value())
            ->setParameter('rowInBlock', $graveSite->rowInBlock()->value())
            ->setParameter('positionInRow', $graveSite->positionInRow()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
