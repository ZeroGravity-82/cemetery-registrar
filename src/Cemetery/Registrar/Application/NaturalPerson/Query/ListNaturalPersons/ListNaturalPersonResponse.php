<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListNaturalPersons;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListNaturalPersonResponse extends ApplicationSuccessResponse
{
    public function __construct(
        NaturalPersonList $list,
        int               $totalCount,
    ) {
        $this->data = (object) [
            'list'       => $list,
            'totalCount' => $totalCount,
        ];
    }
}
