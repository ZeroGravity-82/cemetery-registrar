<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeView
{
    public function __construct(
        public string  $id,
        public string  $treeNumber,
        public ?string $geoPositionLatitude,
        public ?string $geoPositionLongitude,
        public ?string $geoPositionError,
        public string  $createdAt,
        public string  $updatedAt,
    ) {}
}
