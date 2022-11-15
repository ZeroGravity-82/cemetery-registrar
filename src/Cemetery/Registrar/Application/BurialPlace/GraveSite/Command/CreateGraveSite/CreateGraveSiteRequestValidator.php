<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\AbstractGraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateGraveSiteRequestValidator extends AbstractGraveSiteRequestValidator
{
    /**
     * @param CreateGraveSiteRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateUniquenessConstraints($request)
            ->validateCemeteryBlockId($request)
            ->validateRowInBlock($request)
            ->validatePositionInRow($request)
            ->validateGeoPosition($request)
            ->validateSize($request)
            ->validatePersonInChargeId($request)
            ->note();
    }
}
