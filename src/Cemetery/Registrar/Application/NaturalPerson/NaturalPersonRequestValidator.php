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

    protected function validatePassportDetails(ApplicationRequest $request): self
    {
        if (
            $request->passportSeries       !== null ||
            $request->passportNumber       !== null ||
            $request->passportIssuedAt     !== null ||
            $request->passportIssuedBy     !== null ||
            $request->passportDivisionCode !== null
        ) {
            if ($request->passportSeries === null) {
                $this->note->addError('passportSeries', 'Серия паспорта не указана.');
            }
            if ($request->passportNumber === null) {
                $this->note->addError('passportNumber', 'Номер паспорта не указан.');
            }
            if ($request->passportIssuedAt === null) {
                $this->note->addError('passportIssuedAt', 'Дата выдачи паспорта не указана.');
            }
            if ($request->passportIssuedBy === null) {
                $this->note->addError('passportIssuedBy', 'Орган, выдавший паспорт, не указан.');
            }
        }

        return $this;
    }

    protected function validateBornAt(ApplicationRequest $request): self
    {
        if ($request->bornAt !== null &&
            $request->diedAt !== null &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt) >
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt)
        ) {
            $this->note->addError('bornAt', 'Дата рождения не может следовать за датой смерти.');
        }

        return $this;
    }

    protected function validateDiedAt(ApplicationRequest $request): self
    {
        if ($request->bornAt !== null &&
            $request->diedAt !== null &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt) <
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt)
        ) {
            $this->note->addError('diedAt', 'Дата смерти не может предшествовать дате рождения.');
        }

        return $this;
    }

    protected function validateAge(ApplicationRequest $request): self
    {
        if (
            $request->age    !== null &&
            $request->bornAt !== null &&
            $request->diedAt !== null
        ) {
            $this->note->addError('age', 'Возраст не может быть указан, т.к. уже указаны даты рождения и смерти.');
        }

        return $this;
    }
}
