<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\GraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateGraveSiteRequestValidator extends GraveSiteRequestValidator
{
    /**
     * @param CreateGraveSiteRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateCemeteryBlockId($request)
            ->validateRowInBlock($request)
            ->validatePositionInRow($request)
            ->validateGeoPositionLatitude($request)
            ->validateGeoPositionLongitude($request)
            ->validateGeoPositionError($request)
            ->validateSize($request)
            ->note();
    }
}