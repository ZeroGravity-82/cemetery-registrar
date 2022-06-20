<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface Repository
{
    /**
     * Returns the name of the supported aggregate root class.
     *
     * @return string
     */
    public function supportedAggregateRootClassName(): string;

    /**
     * Returns the name of the supported aggregate root ID class.
     *
     * @return string
     */
    public function supportedAggregateRootIdClassName(): string;

    /**
     * Returns the name of the supported aggregate root collection class.
     *
     * @return string
     */
    public function supportedAggregateRootCollectionClassName(): string;

    /**
     * Adds the aggregate root to the repository. If the aggregate root is already persisted, it will be updated.
     *
     * @param $aggregateRoot
     */
    public function save($aggregateRoot): void;

    /**
     * Adds the collection of aggregate roots to the repository. If any of the aggregate roots are already persisted,
     * they will be updated.
     *
     * @param $aggregateRoots
     */
    public function saveAll($aggregateRoots): void;

    /**
     * Returns the aggregate root by the ID. If no aggregate root found, null will be returned.
     *
     * @param $aggregateRootId
     *
     * @return AggregateRoot|null
     */
    public function findById($aggregateRootId): ?AggregateRoot;

    /**
     * Removes the aggregate root from the repository.
     *
     * @param $aggregateRoot
     */
    public function remove($aggregateRoot): void;

    /**
     * Removes the collection of aggregate roots from the repository.
     *
     * @param $aggregateRoots
     */
    public function removeAll($aggregateRoots): void;
}
