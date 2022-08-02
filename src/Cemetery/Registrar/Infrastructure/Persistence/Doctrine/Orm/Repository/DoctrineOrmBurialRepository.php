<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepository extends DoctrineOrmRepository implements BurialRepository
{
    protected function supportedAggregateRootClassName(): string
    {
        return Burial::class;
    }

    protected function supportedAggregateRootIdClassName(): string
    {
        return BurialId::class;
    }

    protected function supportedAggregateRootCollectionClassName(): string
    {
        return BurialCollection::class;
    }

    /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    protected function assertUnique(AggregateRoot $aggregateRoot): void
    {
        // TODO implement
    }
}
