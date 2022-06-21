<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmColumbariumRepository extends DoctrineOrmRepository implements ColumbariumRepository
{
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
}
