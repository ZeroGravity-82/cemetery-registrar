<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteSize;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\AbstractGraveSiteRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteSizeRequestValidator extends AbstractGraveSiteRequestValidator
{
    /**
     * @param ClarifyGraveSiteSizeRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->validateSize($request, true)
            ->note();
    }
}
