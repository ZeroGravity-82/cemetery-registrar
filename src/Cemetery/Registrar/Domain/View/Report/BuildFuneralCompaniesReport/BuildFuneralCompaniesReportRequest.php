<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportRequest extends ApplicationRequest
{
    public function __construct(
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        // TODO add burial type selection
    ) {}
}
