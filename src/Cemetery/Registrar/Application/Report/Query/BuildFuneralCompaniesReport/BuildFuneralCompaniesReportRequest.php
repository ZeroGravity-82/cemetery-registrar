<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Report\Query\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $startDate,
        public ?string $endDate,
        // TODO add burial type selection
    ) {}
}
