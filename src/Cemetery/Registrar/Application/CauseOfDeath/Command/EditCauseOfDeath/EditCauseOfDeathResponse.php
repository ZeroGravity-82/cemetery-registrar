<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathResponse
{
    public function __construct(
        public readonly string $id,
    ) {}
}
