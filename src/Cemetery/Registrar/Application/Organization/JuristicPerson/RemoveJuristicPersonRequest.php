<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonRequest
{
    public function __construct(
        public readonly string $id,
    ) {}
}
