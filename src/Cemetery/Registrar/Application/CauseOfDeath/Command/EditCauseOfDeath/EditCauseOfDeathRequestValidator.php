<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Application\RequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathRequestValidator extends RequestValidator
{
    /**
     * @throw \InvalidArgumentException when the entity ID is not provided or empty
     */
    public function validate(EditCauseOfDeathRequest $request): Notification
    {
        $this->assertValidEntityId($request->id);

        $note = new Notification();
        if (empty(\trim($request->name))) {
            $note->addError('name', 'Причина смерти не может иметь пустое значение.');
        }

        return $note;
    }
}
