<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteListItem
{
    /**
     * @param string      $id
     * @param string      $cemeteryBlockName
     * @param int         $rowInBlock
     * @param int|null    $positionInRow
     * @param string|null $size
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $cemeteryBlockName,
        public readonly int     $rowInBlock,
        public readonly ?int    $positionInRow,
        public readonly ?string $size,
    ) {}
}
