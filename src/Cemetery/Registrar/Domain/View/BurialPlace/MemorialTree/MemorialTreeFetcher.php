<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface MemorialTreeFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param string $id
     *
     * @return MemorialTreeView
     *
     * @throws \RuntimeException when the memorial tree is not found by ID
     */
    public function getViewById(string $id): MemorialTreeView;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return MemorialTreeList
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): MemorialTreeList;

    /**
     * @return int
     */
    public function countTotal(): int;
}
