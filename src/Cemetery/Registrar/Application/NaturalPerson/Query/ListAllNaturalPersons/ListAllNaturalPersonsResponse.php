<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListAllNaturalPersons;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonSimpleList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllNaturalPersonsResponse extends ApplicationSuccessResponse
{
    public function __construct(
        NaturalPersonSimpleList $simpleList,
    ) {
        $this->data = (object) [
            'simpleList' => $simpleList,
        ];
    }
}
