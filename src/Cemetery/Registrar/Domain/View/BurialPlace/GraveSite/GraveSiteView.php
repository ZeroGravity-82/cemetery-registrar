<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteView
{
    public function __construct(
        public string  $id,
        public string  $cemeteryBlockId,
        public int     $rowInBlock,
        public ?int    $positionInRow,
        public ?string $geoPositionLatitude,
        public ?string $geoPositionLongitude,
        public ?string $geoPositionError,
        public ?string $size,
        public string  $createdAt,
        public string  $updatedAt,
    ) {}
}
