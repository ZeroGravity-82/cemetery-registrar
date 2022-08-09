<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CauseOfDeathRequestValidator extends ApplicationRequestValidator
{
    protected function validateName(ApplicationRequest $request): self
    {
        if ($request->name === null || empty(\trim($request->name))) {
            $this->note->addError('name', 'Причина смерти не может иметь пустое наименование.');
        }

        return $this;
    }
}
