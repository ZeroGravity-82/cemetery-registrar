<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\CauseOfDeath\AbstractCauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathRequestValidator extends AbstractCauseOfDeathRequestValidator
{
    /**
     * @param CreateCauseOfDeathRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateUniquenessConstraints($request)
            ->validateName($request)
            ->note();
    }
}
