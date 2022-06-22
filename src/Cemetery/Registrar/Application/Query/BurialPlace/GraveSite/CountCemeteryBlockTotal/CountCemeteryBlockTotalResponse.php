<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountCemeteryBlockTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCemeteryBlockTotalResponse
{
    /**
     * @param int $totalCount
     */
    public function __construct(
        public readonly int $totalCount,
    ) {}
}
