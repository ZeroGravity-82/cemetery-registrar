<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockList
{
    /**
     * @param CemeteryBlockListItem[]|array $items
     */
    public function __construct(
        public readonly array $items,
    ) {}
}
