<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\PaginateCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateCausesOfDeathRequest extends ApplicationRequest
{
    public function __construct(
        public ?int    $page = null,
        public ?string $term = null,
    ) {}
}
