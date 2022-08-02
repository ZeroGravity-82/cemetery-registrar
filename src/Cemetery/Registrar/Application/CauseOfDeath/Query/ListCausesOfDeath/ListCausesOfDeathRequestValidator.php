<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCausesOfDeathRequestValidator
{
    public function validate(ListCausesOfDeathRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
