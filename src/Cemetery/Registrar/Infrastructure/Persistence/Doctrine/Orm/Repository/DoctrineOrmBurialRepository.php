<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepository extends DoctrineOrmRepository implements BurialRepository
{
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
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        // TODO implement
    }
}
