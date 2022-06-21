<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathCollection;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmCauseOfDeathRepository extends DoctrineOrmRepository implements CauseOfDeathRepository
{
    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return CauseOfDeath::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return CauseOfDeathId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return CauseOfDeathCollection::class;
    }
}
