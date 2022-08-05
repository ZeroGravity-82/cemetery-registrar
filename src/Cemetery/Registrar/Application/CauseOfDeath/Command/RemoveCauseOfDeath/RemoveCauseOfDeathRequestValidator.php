<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathRequestValidator
{
    public function validate(RemoveCauseOfDeathRequest $request): Notification
    {
        $note = new Notification();
        if ($request->id === null || empty(\trim($request->id))) {
            $note->addError('id', 'Идентификатор причины смерти не задан или пуст.');
        }

        return $note;
    }
}
