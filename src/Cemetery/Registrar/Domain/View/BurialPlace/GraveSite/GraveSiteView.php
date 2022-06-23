<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteView
{
    /**
     * @param string      $id
     * @param string      $cemeteryBlockId
     * @param int         $rowInBlock
     * @param int|null    $positionInRow
     * @param string|null $geoPositionLatitude
     * @param string|null $geoPositionLongitude
     * @param string|null $geoPositionError
     * @param string|null $size
     * @param string      $createdAt
     * @param string      $updatedAt
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $cemeteryBlockId,
        public readonly int     $rowInBlock,
        public readonly ?int    $positionInRow,
        public readonly ?string $geoPositionLatitude,
        public readonly ?string $geoPositionLongitude,
        public readonly ?string $geoPositionError,
        public readonly ?string $size,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}
}
