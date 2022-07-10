<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathRequest
{
    public ?string $id;
    public ?string $name;
}
