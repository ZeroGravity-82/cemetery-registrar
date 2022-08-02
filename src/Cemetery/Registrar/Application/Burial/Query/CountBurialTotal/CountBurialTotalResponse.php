<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\CountBurialTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountBurialTotalResponse
{
    public function __construct(
        public int $totalCount,
    ) {}
}
