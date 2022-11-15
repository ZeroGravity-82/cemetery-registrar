<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\RemoveJuristicPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
