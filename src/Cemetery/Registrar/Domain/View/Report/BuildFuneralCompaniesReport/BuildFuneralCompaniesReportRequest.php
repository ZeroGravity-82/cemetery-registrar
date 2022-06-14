<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BuildFuneralCompaniesReportRequest
{
    /**
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function __construct(
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        // TODO add burial type selection
    ) {}
}
