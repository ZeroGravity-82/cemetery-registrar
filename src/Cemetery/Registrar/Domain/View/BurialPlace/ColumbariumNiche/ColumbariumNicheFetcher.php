<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumNicheFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param string $id
     *
     * @return ColumbariumNicheView
     *
     * @throws \RuntimeException when the columbarium niche is not found by ID
     */
    public function getViewById(string $id): ColumbariumNicheView;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return ColumbariumNicheList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): ColumbariumNicheList;

    /**
     * @return int
     */
    public function countTotal(): int;
}
