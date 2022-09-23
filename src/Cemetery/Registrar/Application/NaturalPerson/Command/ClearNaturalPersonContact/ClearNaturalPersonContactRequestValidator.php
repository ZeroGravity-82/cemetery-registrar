<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClearNaturalPersonContact;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\NaturalPerson\NaturalPersonRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearNaturalPersonContactRequestValidator extends NaturalPersonRequestValidator
{
    /**
     * @param ClearNaturalPersonContactRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->note();
    }
}
