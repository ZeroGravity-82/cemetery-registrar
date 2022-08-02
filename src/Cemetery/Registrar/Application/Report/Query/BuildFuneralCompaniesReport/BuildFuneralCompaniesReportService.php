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
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        /** @var BuildFuneralCompaniesReportRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var BuildFuneralCompaniesReportRequest $request */
        $startDate = $request->startDate;




    }

    protected function supportedRequestClassName(): string
    {
        return BuildFuneralCompaniesReportRequest::class;
    }
}
