<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CemeteryBlockRequestValidator extends ApplicationRequestValidator
{
    public function __construct(
        private readonly CemeteryBlockFetcher $cemeteryBlockFetcher,
    ) {
        parent::__construct();
    }
    protected function validateName(ApplicationRequest $request): self
    {
        if ($request->name === null || empty(\trim($request->name))) {
            $this->note->addError('name', 'Квартал не может иметь пустое наименование.');
        }
        if (
            $request->name !== null &&
            $this->cemeteryBlockFetcher->doesExistByName($request->name)
        ) {
            $this->note->addError('name', 'Квартал с таким наименованием уже существует.');
        }

        return $this;
    }
}
