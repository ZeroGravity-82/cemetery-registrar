<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CauseOfDeathRequestValidator extends ApplicationRequestValidator
{
    public function __construct(
        private readonly CauseOfDeathFetcher $causeOfDeathFetcher,
    ) {
        parent::__construct();
    }

    protected function validateUniquenessConstraints(ApplicationRequest $request): self
    {
        if (
            $request->name !== null &&
            $this->causeOfDeathFetcher->doesExistByName($request->name)
        ) {
            $this->note->addError('name', 'Причина смерти с таким наименованием уже существует.');
        }

        return $this;
    }

    protected function validateName(ApplicationRequest $request): self
    {
        if ($request->name === null || empty(\trim($request->name))) {
            $this->note->addError('name', 'Причина смерти не может иметь пустое наименование.');
        }

        return $this;
    }
}
