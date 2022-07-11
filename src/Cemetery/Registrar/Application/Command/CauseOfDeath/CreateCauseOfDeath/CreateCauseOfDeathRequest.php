<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathRequest
{
    public function __construct(
        public readonly ?string $name,
    ) {}
}
