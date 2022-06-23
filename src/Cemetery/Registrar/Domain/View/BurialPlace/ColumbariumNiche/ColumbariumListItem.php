<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumListItem
{
    /**
     * @param string $id
     * @param string $name
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {}
}
