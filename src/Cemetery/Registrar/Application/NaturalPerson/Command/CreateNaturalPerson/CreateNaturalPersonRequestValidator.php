<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\CreateNaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\NaturalPerson\NaturalPersonRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateNaturalPersonRequestValidator extends NaturalPersonRequestValidator
{
    /**
     * @param CreateNaturalPersonRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateUniquenessConstraints($request)
            ->validateFullName($request)
            ->validateContact($request, false)
            ->validateBornAt($request)
            ->validatePassport($request)
            ->validateDiedAt($request)
            ->validateAge($request)
            ->validateDeathCertificate($request)
            ->validateCremationCertificate($request)
            ->note();
    }
}
