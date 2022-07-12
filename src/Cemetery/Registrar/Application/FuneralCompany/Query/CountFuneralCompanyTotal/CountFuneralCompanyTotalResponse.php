<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\CountFuneralCompanyTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountFuneralCompanyTotalResponse
{
    /**
     * @param int $totalCount
     */
    public function __construct(
        public readonly int $totalCount,
    ) {}
}
