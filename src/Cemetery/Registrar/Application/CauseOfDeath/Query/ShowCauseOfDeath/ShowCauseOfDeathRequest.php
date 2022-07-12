<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathRequest
{
    public function __construct(
        public readonly ?string $id,
    ) {}
}
