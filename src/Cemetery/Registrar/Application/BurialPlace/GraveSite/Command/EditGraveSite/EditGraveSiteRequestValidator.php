<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditGraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\GraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditGraveSiteRequestValidator extends GraveSiteRequestValidator
{
    /**
     * @param EditGraveSiteRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->validateCemeteryBlockId($request)
            ->validateRowInBlock($request)
            ->validatePositionInRow($request)
            ->validateGeoPosition($request)
            ->validateSize($request)
            ->note();
    }
}
