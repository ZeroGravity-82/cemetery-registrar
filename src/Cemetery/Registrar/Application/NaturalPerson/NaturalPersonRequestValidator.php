<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class NaturalPersonRequestValidator extends ApplicationRequestValidator
{
    public function __construct(
        private readonly NaturalPersonFetcher $naturalPersonFetcher,
    ) {
        parent::__construct();
    }

    protected function validateUniquenessConstraints(ApplicationRequest $request): self
    {
        // TODO implement

        return $this;
    }

    protected function validateFullName(ApplicationRequest $request): self
    {
        if ($request->fullName === null || empty(\trim($request->fullName))) {
            $this->note->addError('fullName', 'ФИО не может иметь пустое значение.');
        }

        return $this;
    }
}
