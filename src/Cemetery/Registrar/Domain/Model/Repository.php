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
     * Returns a repository validator.
     *
     * @return RepositoryValidator
     */
    public function repositoryValidator(): RepositoryValidator;

    /**
     * Adds the aggregate to the repository. If the aggregate is already persisted, it will be updated.
     *
     * @param $aggregateRoot
     *
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     * @throws \RuntimeException         when unique constraints (if any) are violated
     * @throws \RuntimeException         when referential integrity constraints (if any) are violated
     */
    public function save($aggregateRoot): void;

    /**
     * Adds the collection of aggregates to the repository. If any of the aggregates are already persisted, they will
     * be updated.
     *
     * @param $aggregateRoots
     *
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
     * @throws \RuntimeException         when unique constraints (if any) are violated
     * @throws \RuntimeException         when referential integrity constraints (if any) are violated
     */
    public function saveAll($aggregateRoots): void;

    /**
     * Returns the aggregate by the aggregate root ID. If no aggregate root found, null will be returned.
     *
     * @param $aggregateRootId
     *
     * @return AggregateRoot|null
     *
     * @throws \InvalidArgumentException when the aggregate root ID type does not match the repository
     */
    public function findById($aggregateRootId): ?AggregateRoot;

    /**
     * Removes the aggregate from the repository.
     *
     * @param $aggregateRoot
     *
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     * @throws \RuntimeException         when inverse referential integrity constraints (if any) are violated
     */
    public function remove($aggregateRoot): void;

    /**
     * Removes the collection of aggregates from the repository.
     *
     * @param $aggregateRoots
     *
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
     * @throws \RuntimeException         when inverse referential integrity constraints (if any) are violated
     */
    public function removeAll($aggregateRoots): void;
}
