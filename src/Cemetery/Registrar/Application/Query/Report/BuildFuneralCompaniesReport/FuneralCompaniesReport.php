<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Report\BuildFuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompaniesReport
{
    /**
     * @param array  $reportItems
     * @param string $startDate
     * @param string $endDate
     * @param int    $burialTotalCount
     */
    public function __construct(
        public readonly array  $reportItems,
        public readonly string $startDate,
        public readonly string $endDate,
        public readonly int    $burialTotalCount,
    ) {}
}
