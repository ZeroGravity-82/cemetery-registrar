<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateCemeteryBlock;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\AbstractCemeteryBlockRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCemeteryBlockRequestValidator extends AbstractCemeteryBlockRequestValidator
{
    /**
     * @param CreateCemeteryBlockRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        return $this
            ->validateUniquenessConstraints($request)
            ->validateName($request)
            ->note();
    }
}
