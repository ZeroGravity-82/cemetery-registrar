<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathRequestValidator
{
    public function validate(EditCauseOfDeathRequest $request): Notification
    {
        $note = new Notification();
        if (empty(\trim($request->id))) {
            $note->addError('id', 'Идентификатор причины смерти не может иметь пустое значение.');
        }
        if (empty(\trim($request->name))) {
            $note->addError('name', 'Причина смерти не может иметь пустое значение.');
        }

        return $note;
    }
}
