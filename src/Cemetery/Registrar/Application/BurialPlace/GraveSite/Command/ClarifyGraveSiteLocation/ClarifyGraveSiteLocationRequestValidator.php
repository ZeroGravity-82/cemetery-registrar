<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteLocation;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\AbstractGraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteLocationRequestValidator extends AbstractGraveSiteRequestValidator
{
    /**
     * @param ClarifyGraveSiteLocationRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->validateUniquenessConstraints($request)
            ->validateCemeteryBlockId($request)
            ->validateRowInBlock($request)
            ->validatePositionInRow($request)
            ->note();
    }
}
