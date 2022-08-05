<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCausesOfDeathRequestValidator extends ApplicationRequestValidator
{
    /**
     * @param ListCausesOfDeathRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
