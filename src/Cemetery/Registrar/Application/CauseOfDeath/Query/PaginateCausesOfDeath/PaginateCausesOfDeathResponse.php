<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\PaginateCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathPaginatedList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateCausesOfDeathResponse extends ApplicationSuccessResponse
{
    public function __construct(
        CauseOfDeathPaginatedList $paginatedList,
        int                       $totalCount,
    ) {
        $this->data = (object) [
            'paginatedList' => $paginatedList,
            'totalCount'    => $totalCount,
        ];
    }
}
