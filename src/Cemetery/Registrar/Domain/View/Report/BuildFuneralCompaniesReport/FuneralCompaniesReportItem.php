<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\Report\BuildFuneralCompaniesReport;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompaniesReportItem
{
    public function __construct(
        public array  $items,
        public string $funeralCompanyName,
        public int    $burialCount,
    ) {}
}
