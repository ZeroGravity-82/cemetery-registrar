<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractCemeteryBlockRequestValidator extends AbstractApplicationRequestValidator
{
    public function __construct(
        private CemeteryBlockFetcherInterface $cemeteryBlockFetcher,
    ) {
        parent::__construct();
    }

    protected function validateUniquenessConstraints(AbstractApplicationRequest $request): self
    {
        if (
            $request->name !== null &&
            $this->cemeteryBlockFetcher->doesExistByName($request->name)
        ) {
            $this->note->addError('name', 'Квартал с таким наименованием уже существует.');
        }

        return $this;
    }

    protected function validateName(AbstractApplicationRequest $request): self
    {
        if ($request->name === null || empty(\trim($request->name))) {
            $this->note->addError('name', 'Квартал не может иметь пустое наименование.');
        }

        return $this;
    }
}
