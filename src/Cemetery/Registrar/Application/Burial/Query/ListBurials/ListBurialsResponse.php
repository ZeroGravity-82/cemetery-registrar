<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\ListBurials;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeList;
use Cemetery\Registrar\Domain\View\Burial\BurialList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockList;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListBurialsResponse extends ApplicationSuccessResponse
{
    public function __construct(
        BurialList         $list,
        int                $totalCount,
        FuneralCompanyList $funeralCompanyList,
        CemeteryBlockList  $cemeteryBlockList,
        CoffinShapeList    $coffinShapeList,
    ) {
        $this->data = (object) [
            'list'               => $list,
            'totalCount'         => $totalCount,
            'funeralCompanyList' => $funeralCompanyList,
            'cemeteryBlockList'  => $cemeteryBlockList,
            'coffinShapeList'    => $coffinShapeList,
        ];
    }
}
