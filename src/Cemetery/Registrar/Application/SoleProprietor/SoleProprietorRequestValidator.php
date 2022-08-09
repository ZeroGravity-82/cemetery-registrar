<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class SoleProprietorRequestValidator extends ApplicationRequestValidator
{
    protected function validateName(ApplicationRequest $request): self
    {
        if ($request->name === null || empty(\trim($request->name))) {
            $this->note->addError('name', 'ИП не может иметь пустое наименование.');
        }

        return $this;
    }
}
