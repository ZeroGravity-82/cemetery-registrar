<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Report\Query\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport\FuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportResponse
{
    public function __construct(
        public FuneralCompaniesReport $report,
    ) {}
}
