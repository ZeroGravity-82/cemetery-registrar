<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\ListFuneralCompanies;

use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListFuneralCompaniesResponse
{
    public function __construct(
        public FuneralCompanyList $list,
    ) {}
}
