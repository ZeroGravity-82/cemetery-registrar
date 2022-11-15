<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\CreateNaturalPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\NaturalPerson\AbstractNaturalPersonRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateNaturalPersonRequestValidator extends AbstractNaturalPersonRequestValidator
{
    /**
     * @param CreateNaturalPersonRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateUniquenessConstraints($request)
            ->validateFullName($request)
            ->validateContact($request, false)
            ->validateBirthDetails($request, false)
            ->validatePassport($request)
            ->validateDeceasedDetails($request)
            ->note();
    }
}
