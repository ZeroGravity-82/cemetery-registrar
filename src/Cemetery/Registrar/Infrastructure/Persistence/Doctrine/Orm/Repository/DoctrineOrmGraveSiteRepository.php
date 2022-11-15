<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryInterface;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmGraveSiteRepository extends AbstractDoctrineOrmRepository implements GraveSiteRepositoryInterface
{
    protected function supportedAggregateRootClassName(): string
    {
        return GraveSite::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return GraveSiteId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return GraveSiteCollection::class;
    }

    /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    protected function assertUnique(AbstractAggregateRoot $aggregateRoot): void
    {
        /** @var GraveSite $aggregateRoot */
        if ($this->doesSameNicheRowAndPositionAlreadyUsed($aggregateRoot)) {
            throw new Exception('Участок с такими рядом и местом в этом квартале уже существует.');
        }
    }

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
            ->andWhere('gs.cemeteryBlockId = :cemeteryBlockId')
            ->andWhere('gs.rowInBlock = :rowInBlock')
            ->andWhere('gs.positionInRow = :positionInRow')
            ->andWhere('gs.removedAt IS NULL')
            ->setParameter('id', $graveSite->id()->value())
            ->setParameter('cemeteryBlockId', $graveSite->cemeteryBlockId()->value())
            ->setParameter('rowInBlock', $graveSite->rowInBlock()->value())
            ->setParameter('positionInRow', $graveSite->positionInRow()->value())
            ->getQuery()
            ->getSingleScalarResult();
    }
}
