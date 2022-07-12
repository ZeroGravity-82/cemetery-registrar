<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\CreateJuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateJuristicPersonResponse
{
    public function __construct(
        public readonly string $juristicPersonId,
    ) {}
}
