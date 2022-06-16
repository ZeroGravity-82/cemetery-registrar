<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface BurialFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param string $id
     *
     * @return BurialView
     *
     * @throws \RuntimeException when the burial is not found by ID
     */
    public function getViewById(string $id): BurialView;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return BurialList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): BurialList;

    /**
     * @return int
     */
    public function countTotal(): int;
}
