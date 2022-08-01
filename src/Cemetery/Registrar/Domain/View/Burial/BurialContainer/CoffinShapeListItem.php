<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeListItem
{
    public function __construct(
        public string $value,
        public string $label,
    ) {}
}
