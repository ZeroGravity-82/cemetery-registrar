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
    abstract protected function supportedAggregateRootClassName(): string;

    abstract protected function supportedAggregateRootIdClassName(): string;

    abstract protected function supportedAggregateRootCollectionClassName(): string;

    /**
     * @throws Exception when uniqueness constraints (if any) are violated
     */
    abstract protected function assertUnique(AggregateRoot $aggregateRoot): void;

    /**
     * @throws Exception when dependent aggregates exist
     */
    abstract protected function assertNothingRefersTo(AggregateRoot $aggregateRoot): void;

    /**
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     */
    protected function assertSupportedAggregateRootClass(AggregateRoot $aggregateRoot): void
    {
        $supportedAggregateRootClassName = $this->supportedAggregateRootClassName();
        if (!$aggregateRoot instanceof $supportedAggregateRootClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Неподдерживаемый тип корня агрегата: ожидался "%s", "%s" передан.',
                $this->supportedAggregateRootClassName(),
                \get_class($aggregateRoot)
            ));
        }
    }

    /**
     * @throws \InvalidArgumentException when the aggregate root ID type does not match the repository
     */
    protected function assertSupportedAggregateRootIdClass(EntityId $aggregateRootId): void
    {
        $supportedAggregateRootIdClassName = $this->supportedAggregateRootIdClassName();
        if (!$aggregateRootId instanceof $supportedAggregateRootIdClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Неподдерживаемый тип идентификатора корня агрегата: ожидался "%s", "%s" передан.',
                $this->supportedAggregateRootIdClassName(),
                \get_class($aggregateRootId)
            ));
        }
    }

    /**
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
     */
    protected function assertSupportedAggregateRootCollectionClass(EntityCollection $aggregateRoots): void
    {
        $supportedAggregateRootCollectionClassName = $this->supportedAggregateRootCollectionClassName();
        if (!$aggregateRoots instanceof $supportedAggregateRootCollectionClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Неподдерживаемый тип коллекции корней агрегатов: ожидался "%s", "%s" передан.',
                $this->supportedAggregateRootCollectionClassName(),
                \get_class($aggregateRoots)
            ));
        }
    }

    /**
     * @throws Exception when unique constraints (if any) are violated
     */
    protected function doSave(AggregateRoot $aggregateRoot): void
    {
        $this->assertUnique($aggregateRoot);
        $aggregateRoot->refreshUpdatedAtTimestamp();
    }

    protected function doRemove(AggregateRoot $aggregateRoot): void
    {
        $this->assertNothingRefersTo($aggregateRoot);
        $aggregateRoot->refreshRemovedAtTimestamp();
    }
}
