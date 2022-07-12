<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathResponse
{
    public function __construct(
        public readonly CauseOfDeathView $view,
    ) {}
}
