<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath;

use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Application\RequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathRequestValidator extends RequestValidator
{
    /**
     * @throw \InvalidArgumentException when the entity ID is not provided or empty
     */
    public function validate(RemoveCauseOfDeathRequest $request): Notification
    {
        $this->assertValidEntityId($request->id);

        return new Notification();
    }
}
