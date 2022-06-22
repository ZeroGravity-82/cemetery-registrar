<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockList
{
    /**
     * @param array $listItems
     */
    public function __construct(
        public readonly array $listItems,
    ) {}
}
