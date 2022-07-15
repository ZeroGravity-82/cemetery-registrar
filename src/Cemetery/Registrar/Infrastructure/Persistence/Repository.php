<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\EntityCollection;
use Cemetery\Registrar\Domain\Model\EntityId;
use Cemetery\Registrar\Domain\Model\Repository as RepositoryInterface;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @param RepositoryValidator $repositoryValidator
     */
    public function __construct(
        private readonly RepositoryValidator $repositoryValidator,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function repositoryValidator(): RepositoryValidator
    {
        return $this->repositoryValidator;
    }

    /**
     * Checks whether the aggregate root is of a type supported by the repository.
     *
     * @param AggregateRoot $aggregateRoot
     *
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     */
    protected function assertSupportedAggregateRootClass(AggregateRoot $aggregateRoot): void
    {
        $supportedAggregateRootClassName = $this->supportedAggregateRootClassName();
        if (!$aggregateRoot instanceof $supportedAggregateRootClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid type for an aggregate root: expected "%s", "%s" given.',
                $this->supportedAggregateRootClassName(),
                \get_class($aggregateRoot)
            ));
        }
    }

    /**
     * Checks whether the aggregate root ID is of a type supported by the repository.
     *
     * @param EntityId $aggregateRootId
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
     * @param EntityCollection $aggregateRoots
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
}
