<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ShowNaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowNaturalPersonRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
