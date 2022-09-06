<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialList
{
    public function __construct(
        public array   $items,
        public int     $page,
        public int     $pageSize,
        public ?string $term,
        public int     $totalCount,
        public int     $totalPages,
    ) {}
}
