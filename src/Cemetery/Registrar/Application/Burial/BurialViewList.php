<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialViewList
{
    public function __construct(
        public readonly array   $burialViewListItems,
        public readonly int     $page,
        public readonly int     $pageSize,
        public readonly ?string $term,
        public readonly int     $totalCount,
        public readonly int     $totalPages,
    ) {}
}
