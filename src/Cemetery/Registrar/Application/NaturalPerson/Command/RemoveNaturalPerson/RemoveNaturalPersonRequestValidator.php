<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\RemoveNaturalPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\NaturalPerson\AbstractNaturalPersonRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveNaturalPersonRequestValidator extends AbstractNaturalPersonRequestValidator
{
    /**
     * @param RemoveNaturalPersonRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->note();
    }
}
