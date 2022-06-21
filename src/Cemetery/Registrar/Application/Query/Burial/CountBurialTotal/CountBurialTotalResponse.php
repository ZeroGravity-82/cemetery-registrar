<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Burial\CountBurialTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountBurialTotalResponse
{
    /**
     * @param int $totalCount
     */
    public function __construct(
        public readonly int $totalCount,
    ) {}
}
