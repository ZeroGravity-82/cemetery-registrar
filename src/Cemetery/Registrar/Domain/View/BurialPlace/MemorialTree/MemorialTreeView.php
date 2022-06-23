<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeView
{
    /**
     * @param string      $id
     * @param string      $treeNumber
     * @param string|null $geoPositionLatitude
     * @param string|null $geoPositionLongitude
     * @param string|null $geoPositionError
     * @param string      $createdAt
     * @param string      $updatedAt
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $treeNumber,
        public readonly ?string $geoPositionLatitude,
        public readonly ?string $geoPositionLongitude,
        public readonly ?string $geoPositionError,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}
}
