<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListAllCausesOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllCausesOfDeathRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $term = null,
    ) {}
}
