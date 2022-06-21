<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmMemorialTreeRepository extends DoctrineOrmRepository implements MemorialTreeRepository
{
    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootClassName(): string
    {
        return MemorialTree::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootIdClassName(): string
    {
        return MemorialTreeId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function supportedAggregateRootCollectionClassName(): string
    {
        return MemorialTreeCollection::class;
    }
}
