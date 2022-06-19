<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCauseOfDeathTotalResponse
{
    /**
     * @param int $causeOfDeathTotalCount
     */
    public function __construct(
        public readonly int $causeOfDeathTotalCount,
    ) {}
}
