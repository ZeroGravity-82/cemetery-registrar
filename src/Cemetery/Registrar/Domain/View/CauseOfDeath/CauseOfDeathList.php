<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\CauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathList
{
    /**
     * @param CauseOfDeathListItem[]|array $items
     * @param int                          $page
     * @param int                          $pageSize
     * @param string|null                  $term
     * @param int                          $totalCount
     * @param int                          $totalPages
     */
    public function __construct(
        public readonly array   $items,
        public readonly int     $page,
        public readonly int     $pageSize,
        public readonly ?string $term,
        public readonly int     $totalCount,
        public readonly int     $totalPages,
    ) {}
}
