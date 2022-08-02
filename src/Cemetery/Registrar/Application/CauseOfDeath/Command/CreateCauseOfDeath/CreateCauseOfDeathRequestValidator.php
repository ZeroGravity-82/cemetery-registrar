<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathRequestValidator
{
    public function validate(CreateCauseOfDeathRequest $request): Notification
    {
        $note = new Notification();
        if (empty(\trim($request->name))) {
            $note->addError('name', 'Причина смерти не может иметь пустое значение.');
        }

        return $note;
    }
}
