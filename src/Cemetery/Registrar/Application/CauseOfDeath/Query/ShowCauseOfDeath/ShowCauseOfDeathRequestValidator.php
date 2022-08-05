<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\CauseOfDeath\CauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathRequestValidator extends CauseOfDeathRequestValidator
{
    /**
     * @param ShowCauseOfDeathRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->note();
    }
}
