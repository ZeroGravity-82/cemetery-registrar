<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\CountCauseOfDeathTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCauseOfDeathTotalResponse
{
    /**
     * @param int $totalCount
     */
    public function __construct(
        public readonly int $totalCount,
    ) {}
}
