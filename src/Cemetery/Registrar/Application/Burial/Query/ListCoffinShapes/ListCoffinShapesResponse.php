<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\ListCoffinShapes;

use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCoffinShapesResponse
{
    public function __construct(
        public readonly CoffinShapeList $list,
    ) {}
}
