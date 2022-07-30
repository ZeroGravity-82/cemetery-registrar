<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCausesOfDeathRequestValidator
{
    /**
     * @param ListCausesOfDeathRequest $request
     *
     * @return Notification
     */
    public function validate(ListCausesOfDeathRequest $request): Notification
    {
        return new Notification();
    }
}
