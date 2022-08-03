<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListGraveSitesResponse extends ApplicationSuccessResponse
{
    public function __construct(
        GraveSiteList     $list,
        int               $totalCount,
        CemeteryBlockList $cemeteryBlockList,
        int               $cemeteryBlockTotalCount,
    ) {
        $this->data = (object) [
            'list'                    => $list,
            'totalCount'              => $totalCount,
            'cemeteryBlockList'       => $cemeteryBlockList,
            'cemeteryBlockTotalCount' => $cemeteryBlockTotalCount,
        ];
    }
}
