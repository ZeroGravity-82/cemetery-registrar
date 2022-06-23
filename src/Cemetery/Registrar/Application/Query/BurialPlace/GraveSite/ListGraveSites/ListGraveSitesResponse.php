<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\ListGraveSites;

use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListGraveSitesResponse
{
    public function __construct(
        public readonly GraveSiteList $list,
    ) {}
}
