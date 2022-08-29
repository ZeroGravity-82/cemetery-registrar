<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteListItem
{
    public function __construct(
        public string  $id,
        public string  $cemeteryBlockName,
        public int     $rowInBlock,
        public ?int    $positionInRow,
        public ?string $size,
        public ?string $personInChargeFullName,
    ) {}
}
