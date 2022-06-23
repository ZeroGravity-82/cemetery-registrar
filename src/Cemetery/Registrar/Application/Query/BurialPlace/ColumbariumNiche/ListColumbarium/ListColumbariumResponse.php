<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\ColumbariumNiche\ListColumbarium;

use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumResponse
{
    public function __construct(
        public readonly ColumbariumList $list,
    ) {}
}
