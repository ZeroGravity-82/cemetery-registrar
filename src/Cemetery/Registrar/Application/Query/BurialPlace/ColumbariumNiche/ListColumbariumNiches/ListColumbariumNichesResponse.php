<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\ColumbariumNiche\ListColumbariumNiches;

use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumNichesResponse
{
    public function __construct(
        public readonly ColumbariumNicheList $list,
    ) {}
}
