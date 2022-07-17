<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * Repository validator capable of checking for violations of the repository's data integrity constraints.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface RepositoryValidator
{
    /**
     * @param AggregateRoot $aggregateRoot
     * @param Repository    $repository
     *
     * @throws \RuntimeException when unique constraints (if any) are violated
     */
    public function assertUnique(AggregateRoot $aggregateRoot, Repository $repository): void;

    /**
     * @param AggregateRoot $aggregateRoot
     * @param Repository    $repository
     *
     * @throws \RuntimeException when referential integrity constraints (if any) are violated
     */
    public function assertReferencesNotBroken(AggregateRoot $aggregateRoot, Repository $repository): void;

    /**
     * @param AggregateRoot $aggregateRoot
     * @param Repository    $repository
     *
     * @throws \RuntimeException when inverse referential integrity constraints (if any) will be violated after removing
     */
    public function assertRemovable(AggregateRoot $aggregateRoot, Repository $repository): void;
}
