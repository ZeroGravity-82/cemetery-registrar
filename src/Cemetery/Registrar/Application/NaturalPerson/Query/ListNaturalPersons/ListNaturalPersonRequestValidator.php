<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListNaturalPersons;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListNaturalPersonRequestValidator extends ApplicationRequestValidator
{
    /**
     * @param ListNaturalPersonRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
