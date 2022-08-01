<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheView
{
    public function __construct(
        public string  $id,
        public string  $columbariumId,
        public int     $rowInColumbarium,
        public string  $nicheNumber,
        public ?string $geoPositionLatitude,
        public ?string $geoPositionLongitude,
        public ?string $geoPositionError,
        public string  $createdAt,
        public string  $updatedAt,
    ) {}
}
