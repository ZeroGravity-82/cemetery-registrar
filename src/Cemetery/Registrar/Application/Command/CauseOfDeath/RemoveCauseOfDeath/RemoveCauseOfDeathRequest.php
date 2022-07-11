<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\CauseOfDeath\RemoveCauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathRequest
{
    public function __construct(
        public readonly ?string $id,
    ) {}
}
