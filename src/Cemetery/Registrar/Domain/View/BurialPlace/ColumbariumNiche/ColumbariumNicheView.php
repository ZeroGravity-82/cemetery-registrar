<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheView
{
    /**
     * @param string      $id
     * @param string      $columbariumId
     * @param int         $rowInColumbarium
     * @param int         $nicheNumber
     * @param string|null $geoPositionLatitude
     * @param string|null $geoPositionLongitude
     * @param string|null $geoPositionError
     * @param string      $createdAt
     * @param string      $updatedAt
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $columbariumId,
        public readonly int     $rowInColumbarium,
        public readonly int     $nicheNumber,
        public readonly ?string $geoPositionLatitude,
        public readonly ?string $geoPositionLongitude,
        public readonly ?string $geoPositionError,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}
}
