<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\DiscardGraveSitePersonInCharge;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\GraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DiscardGraveSitePersonInChargeRequestValidator extends GraveSiteRequestValidator
{
    /**
     * @param DiscardGraveSitePersonInChargeRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->note();
    }
}
