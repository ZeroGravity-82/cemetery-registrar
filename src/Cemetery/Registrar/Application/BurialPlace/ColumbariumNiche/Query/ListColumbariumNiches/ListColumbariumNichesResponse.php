<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumNichesResponse extends ApplicationSuccessResponse
{
    public function __construct(
        ColumbariumNicheList $list,
        int                  $totalCount,
        ColumbariumList      $columbariumList,
        int                  $columbariumTotalCount,
    ) {
        $this->data = (object) [
            'list'                  => $list,
            'totalCount'            => $totalCount,
            'columbariumList'       => $columbariumList,
            'columbariumTotalCount' => $columbariumTotalCount,
        ];
    }
}
