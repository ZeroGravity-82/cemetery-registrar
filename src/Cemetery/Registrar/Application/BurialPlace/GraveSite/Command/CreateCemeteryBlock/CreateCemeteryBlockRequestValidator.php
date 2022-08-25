<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateCemeteryBlock;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\CemeteryBlockRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCemeteryBlockRequestValidator extends CemeteryBlockRequestValidator
{
    /**
     * @param CreateCemeteryBlockRequest $request
     */
    public function validate(ApplicationRequest $request): Notification
    {
        return $this
            ->validateName($request)
            ->validateUniquenessConstraints($request)
            ->note();
    }
}
