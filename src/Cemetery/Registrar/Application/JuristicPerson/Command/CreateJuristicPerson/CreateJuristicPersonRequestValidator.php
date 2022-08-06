<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\CreateJuristicPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\JuristicPerson\JuristicPersonRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateJuristicPersonRequestValidator extends JuristicPersonRequestValidator
{
    /**
     * @param CreateJuristicPersonRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateName($request)
            ->note();
    }
}
