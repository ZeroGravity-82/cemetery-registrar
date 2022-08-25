<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\CauseOfDeath\CauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathRequestValidator extends CauseOfDeathRequestValidator
{
    /**
     * @param CreateCauseOfDeathRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateName($request)
            ->validateUniquenessConstraints($request)
            ->note();
    }
}
