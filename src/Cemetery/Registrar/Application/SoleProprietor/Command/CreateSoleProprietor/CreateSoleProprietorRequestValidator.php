<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor\Command\CreateSoleProprietor;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Application\SoleProprietor\SoleProprietorRequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateSoleProprietorRequestValidator extends SoleProprietorRequestValidator
{
    /**
     * @param CreateSoleProprietorRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateName($request)
            ->note();
    }
}
