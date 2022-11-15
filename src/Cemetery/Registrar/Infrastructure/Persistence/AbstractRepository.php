<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\Model\AbstractEntityCollection;
use Cemetery\Registrar\Domain\Model\AbstractEntityId;
use Cemetery\Registrar\Domain\Model\RepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    abstract protected function supportedAggregateRootClassName(): string;

    abstract protected function supportedAggregateRootIdClassName(): string;

    abstract protected function supportedAggregateRootCollectionClassName(): string;

    /**
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     */
    protected function assertSupportedAggregateRootClass(AbstractAggregateRoot $aggregateRoot): void
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
    protected function assertSupportedAggregateRootIdClass(AbstractEntityId $aggregateRootId): void
    {
        $supportedAggregateRootIdClassName = $this->supportedAggregateRootIdClassName();
        if (!$aggregateRootId instanceof $supportedAggregateRootIdClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Неподдерживаемый тип ID корня агрегата: ожидался "%s", "%s" передан.',
                $this->supportedAggregateRootIdClassName(),
                \get_class($aggregateRootId)
            ));
        }
    }

    /**
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
     */
    protected function assertSupportedAggregateRootCollectionClass(AbstractEntityCollection $aggregateRoots): void
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
}
