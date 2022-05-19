<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface BurialFetcher
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @param string $id
     *
     * @return BurialFormView
     *
     * @throws \RuntimeException when the burial is not found by ID
     */
    public function getById(string $id): BurialFormView;

    /**
     * @param int         $page
     * @param string|null $term
     * @param int         $pageSize
     *
     * @return BurialViewListItem[]|array
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): array;
}
