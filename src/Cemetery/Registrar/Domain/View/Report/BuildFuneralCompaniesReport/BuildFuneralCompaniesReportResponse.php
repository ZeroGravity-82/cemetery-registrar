<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportResponse
{
    /**
     * @param FuneralCompaniesReport $report
     */
    public function __construct(
        public readonly FuneralCompaniesReport $report,
    ) {}
}
