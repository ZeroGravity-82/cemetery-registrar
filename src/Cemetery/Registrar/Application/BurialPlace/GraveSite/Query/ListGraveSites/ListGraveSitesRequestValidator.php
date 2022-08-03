<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListGraveSitesRequestValidator
{
    public function validate(ListGraveSitesRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
