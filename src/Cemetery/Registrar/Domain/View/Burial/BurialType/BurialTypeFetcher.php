<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial\BurialType;

use Cemetery\Registrar\Domain\Model\Burial\BurialType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTypeFetcher
{
    /**
     * @return BurialTypeList
     */
    public function findAll(): BurialTypeList
    {
        $coffinShapes = [
            BurialType::COFFIN_IN_GRAVE_SITE,
            BurialType::URN_IN_GRAVE_SITE,
            BurialType::URN_IN_COLUMBARIUM_NICHE,
            BurialType::ASHES_UNDER_MEMORIAL_TREE,
        ];

        return new BurialTypeList(\array_map(
            function ($item) { return new BurialTypeListItem($item, BurialType::LABELS[$item]); },
            $coffinShapes
        ));
    }
}
