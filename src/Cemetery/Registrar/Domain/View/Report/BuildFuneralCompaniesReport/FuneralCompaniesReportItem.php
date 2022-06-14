<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompaniesReportItem
{
    /**
     * @param array  $reportItems
     * @param string $funeralCompanyName
     * @param int    $burialCount
     */
    public function __construct(
        public readonly array  $reportItems,
        public readonly string $funeralCompanyName,
        public readonly int    $burialCount,
    ) {}
}
