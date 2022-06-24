<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeListItem
{
    /**
     * @param string $value
     * @param string $label
     */
    public function __construct(
        public readonly string $value,
        public readonly string $label,
    ) {}
}
