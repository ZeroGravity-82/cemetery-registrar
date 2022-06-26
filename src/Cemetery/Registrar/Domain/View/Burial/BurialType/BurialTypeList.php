<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Burial\BurialType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTypeList
{
    /**
     * @param BurialTypeListItem[]|array $listItems
     */
    public function __construct(
        public readonly array $listItems,
    ) {}
}
