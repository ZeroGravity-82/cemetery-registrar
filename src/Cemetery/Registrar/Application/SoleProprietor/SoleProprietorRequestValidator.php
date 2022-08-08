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
    protected function validateId(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'id') &&
            ($request->id === null || empty(\trim($request->id)))
        ) {
            $this->note->addError('id', 'Идентификатор ИП не задан.');
        }

        return $this;
    }

    protected function validateName(ApplicationRequest $request): self
    {
        if (
            $this->hasProperty($request, 'name') &&
            ($request->name === null || empty(\trim($request->name)))
        ) {
            $this->note->addError('name', 'ИП не может иметь пустое наименование.');
        }

        return $this;
    }
}
