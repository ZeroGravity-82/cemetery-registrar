<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationService;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportService extends ApplicationService
{
    /**
     * {@inheritdoc}
     */
    public function execute($request): BuildFuneralCompaniesReportResponse
    {
        $this->assertInstanceOf($request, BuildFuneralCompaniesReportRequest::class);

        /** @var BuildFuneralCompaniesReportRequest $request */
        $startDate = $request->startDate;




    }
}
