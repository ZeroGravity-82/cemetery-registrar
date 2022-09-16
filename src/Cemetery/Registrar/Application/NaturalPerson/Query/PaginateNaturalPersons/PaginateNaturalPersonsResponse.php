<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\PaginateNaturalPersons;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonPaginatedList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateNaturalPersonsResponse extends ApplicationSuccessResponse
{
    public function __construct(
        NaturalPersonPaginatedList $list,
        int                        $totalCount,
    ) {
        $this->data = (object) [
            'list'       => $list,
            'totalCount' => $totalCount,
        ];
    }
}
