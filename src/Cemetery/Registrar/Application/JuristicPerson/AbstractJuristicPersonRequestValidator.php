<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractJuristicPersonRequestValidator extends AbstractApplicationRequestValidator
{
    protected function validateName(AbstractApplicationRequest $request): self
    {
        if ($request->name === null || empty(\trim($request->name))) {
            $this->note->addError('name', 'Юрлицо не может иметь пустое наименование.');
        }

        return $this;
    }
}
