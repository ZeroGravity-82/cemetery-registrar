<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\CauseOfDeath\AbstractCauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathRequestValidator extends AbstractCauseOfDeathRequestValidator
{
    /**
     * @param EditCauseOfDeathRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->validateUniquenessConstraints($request)
            ->validateName($request)
            ->note();
    }
}
