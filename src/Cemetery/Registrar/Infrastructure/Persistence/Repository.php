<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\EntityCollection;
use Cemetery\Registrar\Domain\Model\EntityId;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Repository as RepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * Returns the name of the supported aggregate root class.
     */
    abstract protected function supportedAggregateRootClassName(): string;

    /**
     * Returns the name of the supported aggregate root ID class.
     */
    abstract protected function supportedAggregateRootIdClassName(): string;

    /**
     * Returns the name of the supported aggregate root collection class.
     */
    abstract protected function supportedAggregateRootCollectionClassName(): string;

    /**
     * Checks whether the aggregate root meets uniqueness constraints (if any).
     *
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    abstract protected function assertUnique(AggregateRoot $aggregateRoot): void;

    /**
     * Checks whether the aggregate root is of a type supported by the repository.
     *
     * @throws \LogicException when the aggregate root type does not match the repository
     */
    protected function assertSupportedAggregateRootClass(AggregateRoot $aggregateRoot): void
    {
        $supportedAggregateRootClassName = $this->supportedAggregateRootClassName();
        if (!$aggregateRoot instanceof $supportedAggregateRootClassName) {
            throw new \LogicException(\sprintf(
                'Неподдерживаемый тип корня агрегата: ожидался "%s", "%s" передан.',
                $this->supportedAggregateRootClassName(),
                \get_class($aggregateRoot)
            ));
        }
    }

    /**
     * Checks whether the aggregate root ID is of a type supported by the repository.
     *
     * @throws \InvalidArgumentException when the aggregate root ID type does not match the repository
     */
    protected function assertSupportedAggregateRootIdClass(EntityId $aggregateRootId): void
    {
        $supportedAggregateRootIdClassName = $this->supportedAggregateRootIdClassName();
        if (!$aggregateRootId instanceof $supportedAggregateRootIdClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid type for an aggregate root ID: expected "%s", "%s" given.',
                $this->supportedAggregateRootIdClassName(),
                \get_class($aggregateRootId)
            ));
        }
    }

    /**
     * Checks whether the aggregate root collection is of a type supported by the repository.
     *
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
     */
    protected function assertSupportedAggregateRootCollectionClass(EntityCollection $aggregateRoots): void
    {
        $supportedAggregateRootCollectionClassName = $this->supportedAggregateRootCollectionClassName();
        if (!$aggregateRoots instanceof $supportedAggregateRootCollectionClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid type for an aggregate root collection: expected "%s", "%s" given.',
                $this->supportedAggregateRootCollectionClassName(),
                \get_class($aggregateRoots)
            ));
        }
    }

    /**
     * @throws \RuntimeException when unique constraints (if any) are violated
     */
    protected function doSave(AggregateRoot $aggregateRoot): void
    {
        $this->assertUnique($aggregateRoot);
        $aggregateRoot->refreshUpdatedAtTimestamp();
    }

    protected function doRemove(AggregateRoot $aggregateRoot): void
    {
        $aggregateRoot->refreshRemovedAtTimestamp();
    }
}
