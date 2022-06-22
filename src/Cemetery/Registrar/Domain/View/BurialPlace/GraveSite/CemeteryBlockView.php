<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockView
{
    /**
     * @param string $id
     * @param string $name
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $createdAt,
        public readonly string $updatedAt,
    ) {}
}
