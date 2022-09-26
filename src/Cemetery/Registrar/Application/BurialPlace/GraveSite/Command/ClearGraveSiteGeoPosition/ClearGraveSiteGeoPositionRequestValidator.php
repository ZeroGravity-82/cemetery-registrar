<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClearGraveSiteGeoPosition;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\GraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearGraveSiteGeoPositionRequestValidator extends GraveSiteRequestValidator
{
    /**
     * @param ClearGraveSiteGeoPositionRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->note();
    }
}
