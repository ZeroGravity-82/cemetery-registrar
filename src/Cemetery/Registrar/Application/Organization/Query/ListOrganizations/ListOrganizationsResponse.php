<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\Query\ListOrganizations;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\Organization\OrganizationList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListOrganizationsResponse extends ApplicationSuccessResponse
{
    public function __construct(
        OrganizationList $list,
        int              $totalCount,
    ) {
        $this->data = (object) [
            'list'       => $list,
            'totalCount' => $totalCount,
        ];
    }
}
