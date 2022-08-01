<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompaniesReport
{
    public function __construct(
        public array  $items,
        public string $startDate,
        public string $endDate,
        public int    $burialTotalCount,
    ) {}
}
