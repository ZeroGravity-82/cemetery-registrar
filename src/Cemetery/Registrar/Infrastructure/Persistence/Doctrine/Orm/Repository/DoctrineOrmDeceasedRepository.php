<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Deceased\Deceased;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedCollection;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmDeceasedRepository extends DoctrineOrmRepository implements DeceasedRepository
{
    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return Deceased::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return DeceasedId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return DeceasedCollection::class;
    }
}
