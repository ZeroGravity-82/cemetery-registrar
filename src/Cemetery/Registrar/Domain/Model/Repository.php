<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface Repository
{
    /**
     * Adds the aggregate to the repository. If the aggregate is already persisted, it will be updated.
     *
     * @throws \LogicException when the aggregate root type does not match the repository
     * @throws Exception       when uniqueness constraints (if any) are violated
     */
    public function save($aggregateRoot): void;

    /**
     * Adds the collection of aggregates to the repository. If any of the aggregates are already persisted, they will
     * be updated.
     *
     * @throws \LogicException when the aggregate root collection type does not match the repository
     * @throws Exception       when uniqueness constraints (if any) are violated
     */
    public function saveAll($aggregateRoots): void;

    /**
     * Returns the aggregate by the aggregate root ID. If no aggregate root found, null will be returned.
     *
     * @throws \LogicException when the aggregate root ID type does not match the repository
     */
    public function findById($aggregateRootId): ?AggregateRoot;

    /**
     * Removes the aggregate from the repository.
     *
     * @throws \LogicException when the aggregate root type does not match the repository
     */
    public function remove($aggregateRoot): void;

    /**
     * Removes the collection of aggregates from the repository.
     *
     * @throws \LogicException when the aggregate root collection type does not match the repository
     */
    public function removeAll($aggregateRoots): void;
}
