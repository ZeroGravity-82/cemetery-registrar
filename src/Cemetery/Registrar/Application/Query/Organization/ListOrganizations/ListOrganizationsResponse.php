<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Organization\ListOrganizations;

use Cemetery\Registrar\Domain\View\Organization\OrganizationList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListOrganizationsResponse
{
    public function __construct(
        public readonly OrganizationList $list,
    ) {}
}
