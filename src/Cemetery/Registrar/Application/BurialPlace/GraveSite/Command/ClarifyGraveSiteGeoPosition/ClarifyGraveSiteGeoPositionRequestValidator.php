<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteGeoPosition;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\AbstractGraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteGeoPositionRequestValidator extends AbstractGraveSiteRequestValidator
{
    /**
     * @param ClarifyGraveSiteGeoPositionRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->validateGeoPosition($request, true)
            ->note();
    }
}
