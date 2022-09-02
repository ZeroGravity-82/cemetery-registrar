<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListNaturalPersons;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListNaturalPersonsRequest extends ApplicationRequest
{
    public function __construct(
        public ?int    $page     = null,
        public ?int    $pageSize = null,
        public ?string $term     = null,
    ) {}
}
