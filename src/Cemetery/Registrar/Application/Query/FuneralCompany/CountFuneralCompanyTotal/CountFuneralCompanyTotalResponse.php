<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\FuneralCompany\CountFuneralCompanyTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountFuneralCompanyTotalResponse
{
    /**
     * @param int $funeralCompanyTotalCount
     */
    public function __construct(
        public readonly int $funeralCompanyTotalCount,
    ) {}
}
