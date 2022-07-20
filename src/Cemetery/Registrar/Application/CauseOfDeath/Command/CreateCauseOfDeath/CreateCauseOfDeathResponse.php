<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathResponse
{
    public function __construct(
        public readonly string $id,
    ) {}


    // isSuccess
    // errors
}
