<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumList
{
    /**
     * @param array $listItems
     */
    public function __construct(
        public readonly array $listItems,
    ) {}
}
