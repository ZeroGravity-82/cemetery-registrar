<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListAliveNaturalPersons;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAliveNaturalPersonsRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $term = null,
    ) {}
}
