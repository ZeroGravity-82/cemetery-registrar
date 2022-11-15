<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditCemeteryBlock;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\AbstractCemeteryBlockRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCemeteryBlockRequestValidator extends AbstractCemeteryBlockRequestValidator
{
    /**
     * @param EditCemeteryBlockRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateId($request)
            ->validateUniquenessConstraints($request)
            ->validateName($request)
            ->note();
    }
}
