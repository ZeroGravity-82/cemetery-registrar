<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListAllNaturalPersons;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllNaturalPersonsRequestValidator extends ApplicationRequestValidator
{
    /**
     * @param ListAllNaturalPersonsRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
