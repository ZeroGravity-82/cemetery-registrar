<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeList
{
    public function __construct(
        public array $items,
    ) {}
}
