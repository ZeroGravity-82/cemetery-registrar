<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\ApplicationService;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportService extends ApplicationService
{
    /**
     * @param BuildFuneralCompaniesReportRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        $this->assertSupportedRequestClass($request);

        /** @var BuildFuneralCompaniesReportRequest $request */
        $startDate = $request->startDate;




    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return BuildFuneralCompaniesReportRequest::class;
    }
}
