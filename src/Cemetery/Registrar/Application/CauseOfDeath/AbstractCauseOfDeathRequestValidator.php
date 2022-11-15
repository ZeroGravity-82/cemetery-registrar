<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractCauseOfDeathRequestValidator extends AbstractApplicationRequestValidator
{
    public function __construct(
        private CauseOfDeathFetcherInterface $causeOfDeathFetcher,
    ) {
        parent::__construct();
    }

    protected function validateUniquenessConstraints(AbstractApplicationRequest $request): self
    {
        if (
            $request->name !== null &&
            $this->causeOfDeathFetcher->doesExistByName($request->name)
        ) {
            $this->note->addError('name', 'Причина смерти с таким наименованием уже существует.');
        }

        return $this;
    }

    protected function validateName(AbstractApplicationRequest $request): self
    {
        if ($request->name === null || empty(\trim($request->name))) {
            $this->note->addError('name', 'Причина смерти не может иметь пустое наименование.');
        }

        return $this;
    }
}
