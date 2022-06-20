<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationService;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportService extends ApplicationService
{
    public function supportedRequestClassName(): string
    {
        return BuildFuneralCompaniesReportRequest::class;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($request): BuildFuneralCompaniesReportResponse
    {
        $this->assertSupportedRequestClass($request);

        /** @var BuildFuneralCompaniesReportRequest $request */
        $startDate = $request->startDate;




    }
}
