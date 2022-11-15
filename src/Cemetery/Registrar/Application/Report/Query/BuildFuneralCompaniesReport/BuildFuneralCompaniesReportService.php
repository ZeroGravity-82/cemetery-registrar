<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Report\Query\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportService extends AbstractApplicationService
{
    public function __construct(
        BuildFuneralCompaniesReportRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var BuildFuneralCompaniesReportRequest $request */
        $startDate = $request->startDate;




    }

    protected function supportedRequestClassName(): string
    {
        return BuildFuneralCompaniesReportRequest::class;
    }
}
