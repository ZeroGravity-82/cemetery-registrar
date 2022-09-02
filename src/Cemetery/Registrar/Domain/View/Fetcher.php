<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface Fetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * Returns a view for an entity by the ID. If there is no data, null will be returned.
     */
    public function findViewById(string $id): mixed;

    /**
     * Checks if the entity exists by the ID.
     */
    public function doesExistById(string $id): bool;

    /**
     * Returns a list of items according to the given criteria.
     */
    public function findAll(?int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): mixed;

    /**
     * Counts the total count of items.
     */
    public function countTotal(): int;
}
