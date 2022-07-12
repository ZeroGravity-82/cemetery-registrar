<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathRequest
{
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
    ) {}
}
