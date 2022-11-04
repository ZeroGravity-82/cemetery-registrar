<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCemeteryBlocksResponse extends ApplicationSuccessResponse
{
    public function __construct(
        CemeteryBlockList $list,
        int               $totalCount,
    ) {
        $this->data = (object) [
            'list'       => $list,
            'totalCount' => $totalCount,
        ];
    }
}
