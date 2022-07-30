<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\CountCauseOfDeathTotal;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCauseOfDeathTotalRequestValidator
{
    /**
     * @param CountCauseOfDeathTotalRequest $request
     *
     * @return Notification
     */
    public function validate(CountCauseOfDeathTotalRequest $request): Notification
    {
        return new Notification();
    }
}
