<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\RemoveJuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonRequest
{
    public function __construct(
        public readonly string $id,
    ) {}
}
