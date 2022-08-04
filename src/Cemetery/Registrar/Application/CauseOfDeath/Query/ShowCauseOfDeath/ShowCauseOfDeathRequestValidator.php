<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Application\RequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathRequestValidator extends RequestValidator
{
    /**
     * @throw \InvalidArgumentException when the entity ID is not provided or empty
     */
    public function validate(ShowCauseOfDeathRequest $request): Notification
    {
        $this->assertValidEntityId($request->id);

        return new Notification();
    }
}
