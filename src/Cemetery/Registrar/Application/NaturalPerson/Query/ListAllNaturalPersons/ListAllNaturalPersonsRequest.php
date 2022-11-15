<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListAllNaturalPersons;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllNaturalPersonsRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $term = null,
    ) {}
}
