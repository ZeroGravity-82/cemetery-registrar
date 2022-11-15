<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\CreateJuristicPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\JuristicPerson\AbstractJuristicPersonRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateJuristicPersonRequestValidator extends AbstractJuristicPersonRequestValidator
{
    /**
     * @param CreateJuristicPersonRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateName($request)
            ->note();
    }
}
