<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\CountColumbariumTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountColumbariumTotalResponse
{
    /**
     * @param int $totalCount
     */
    public function __construct(
        public readonly int $totalCount,
    ) {}
}
