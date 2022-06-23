<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface GraveSiteFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param string $id
     *
     * @return GraveSiteView
     *
     * @throws \RuntimeException when the grave site is not found by ID
     */
    public function getViewById(string $id): GraveSiteView;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return GraveSiteList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): GraveSiteList;

    /**
     * @return int
     */
    public function countTotal(): int;
}
