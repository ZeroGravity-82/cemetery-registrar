<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\MemorialTree\CountMemorialTreeTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountMemorialTreeTotalResponse
{
    /**
     * @param int $totalCount
     */
    public function __construct(
        public readonly int $totalCount,
    ) {}
}
