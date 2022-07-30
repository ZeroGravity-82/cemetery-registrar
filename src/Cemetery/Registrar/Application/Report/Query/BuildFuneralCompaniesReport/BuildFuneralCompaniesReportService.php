<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Report\Query\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportService extends ApplicationService
{
    /**
     * @param BuildFuneralCompaniesReportRequest $request
     *
     * @return Notification
     */
    public function validate(ApplicationRequest $request): Notification
    {
        // TODO: Implement validate() method.
    }

    /**
     * @param BuildFuneralCompaniesReportRequest $request
     *
     * @return ApplicationSuccessResponse
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $this->assertSupported($request);

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
