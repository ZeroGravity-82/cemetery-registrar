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
     *
     * @param string $id
     *
     * @return mixed
     */
    public function findViewById(string $id): mixed;

    /**
     * Returns a list of items according to the given criteria.
     *
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return mixed
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): mixed;

    /**
     * Counts the total count of items.
     *
     * @return int
     */
    public function countTotal(): int;
}
