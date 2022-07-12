<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeList
{
    /**
     * @param CoffinShapeListItem[]|array $items
     */
    public function __construct(
        public readonly array $items,
    ) {}
}
