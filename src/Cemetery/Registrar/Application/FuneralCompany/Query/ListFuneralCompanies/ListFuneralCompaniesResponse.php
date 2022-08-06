<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\ListFuneralCompanies;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListFuneralCompaniesResponse extends ApplicationSuccessResponse
{
    public function __construct(
        FuneralCompanyList $list,
        int                $totalCount,
    ) {
        $this->data = (object) [
            'list'       => $list,
            'totalCount' => $totalCount,
        ];
    }
}
