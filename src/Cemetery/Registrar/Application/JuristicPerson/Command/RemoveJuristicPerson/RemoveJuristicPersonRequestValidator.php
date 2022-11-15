<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\RemoveJuristicPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\JuristicPerson\AbstractJuristicPersonRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonRequestValidator extends AbstractJuristicPersonRequestValidator
{
    /**
     * @param RemoveJuristicPersonRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->note();
    }
}
