<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\PaginateCausesOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateCausesOfDeathRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?int    $page = null,
        public ?string $term = null,
    ) {}
}
