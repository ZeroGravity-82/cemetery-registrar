<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\CauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathList
{
    public function __construct(
        public array   $items,
        public ?int    $page,
        public int     $pageSize,
        public ?string $term,
        public int     $totalCount,
        public ?int    $totalPages,
    ) {}
}
