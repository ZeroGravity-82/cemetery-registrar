<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\CountCauseOfDeathTotal;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCauseOfDeathTotalRequestValidator
{
    public function validate(CountCauseOfDeathTotalRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
