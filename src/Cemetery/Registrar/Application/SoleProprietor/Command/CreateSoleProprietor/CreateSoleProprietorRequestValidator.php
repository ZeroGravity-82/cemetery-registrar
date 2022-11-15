<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor\Command\CreateSoleProprietor;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Application\SoleProprietor\AbstractSoleProprietorRequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateSoleProprietorRequestValidator extends AbstractSoleProprietorRequestValidator
{
    /**
     * @param CreateSoleProprietorRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateName($request)
            ->note();
    }
}
