<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathRequestValidator
{
    /**
     * @param CreateCauseOfDeathRequest $request
     *
     * @return Notification
     */
    public function validate(CreateCauseOfDeathRequest $request): Notification
    {
        $note = new Notification();



        return $note;
    }
}
