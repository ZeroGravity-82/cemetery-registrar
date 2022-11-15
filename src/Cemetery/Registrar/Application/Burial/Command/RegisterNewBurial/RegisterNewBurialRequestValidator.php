<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Command\RegisterNewBurial;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RegisterNewBurialRequestValidator extends AbstractApplicationRequestValidator
{
    /**
     * @param RegisterNewBurialRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
