<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListAllCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllCausesOfDeathRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $term = null,
    ) {}
}
