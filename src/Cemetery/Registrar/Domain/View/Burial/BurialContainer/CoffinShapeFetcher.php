<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeFetcher
{
    public function findAll(): CoffinShapeList
    {
        $coffinShapes = [
            CoffinShape::TRAPEZOID,
            CoffinShape::GREEK_WITH_HANDLES,
            CoffinShape::GREEK_WITHOUT_HANDLES,
            CoffinShape::AMERICAN,
        ];

        return new CoffinShapeList(\array_map(
            function ($item) { return new CoffinShapeListItem($item, CoffinShape::LABELS[$item]); },
            $coffinShapes
        ));
    }
}
