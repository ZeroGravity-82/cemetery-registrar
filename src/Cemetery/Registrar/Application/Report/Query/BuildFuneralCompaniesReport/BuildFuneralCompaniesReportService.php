<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Report\Query\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportService extends ApplicationService
{
    public function __construct(
        BuildFuneralCompaniesReportRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
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
