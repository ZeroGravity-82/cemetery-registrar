<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath;

use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCausesOfDeathResponse
{
    public function __construct(
        public readonly CauseOfDeathList $list,
    ) {}
}
