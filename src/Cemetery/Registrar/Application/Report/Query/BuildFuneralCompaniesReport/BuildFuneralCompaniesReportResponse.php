<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Report\Query\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport\FuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportResponse extends ApplicationSuccessResponse
{
    public function __construct(
        FuneralCompaniesReport $report,
    ) {
        $this->data = (object) [
            'report' => $report,
        ];
    }
}
