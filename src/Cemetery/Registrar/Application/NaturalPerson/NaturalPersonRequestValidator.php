<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationRequestValidator;
use Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonBirthDetails\ClarifyNaturalPersonBirthDetailsRequest;
use Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonContact\ClarifyNaturalPersonContactRequest;
use Cemetery\Registrar\Domain\Model\Contact\Email;
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

    protected function validateContact(ApplicationRequest $request, bool $isRequired = false): self
    {
        if (
            $isRequired &&
            ($request->phone           === null || \trim($request->phone)           === '') &&
            ($request->phoneAdditional === null || \trim($request->phoneAdditional) === '') &&
            ($request->address         === null || \trim($request->address)         === '') &&
            ($request->email           === null || \trim($request->email)           === '')
        ) {
            $message = 'Контактные данные не указаны.';
            $this->note->addError('phone', $message);
            $this->note->addError('phoneAdditional', $message);
            $this->note->addError('address', $message);
            $this->note->addError('email', $message);
        }
        if ($request->email !== null && !Email::isValidFormat($request->email)) {
            $this->note->addError('email', 'Неверный формат адреса электронной почты.');
        }

        return $this;
    }

    protected function validateBirthDetails(ApplicationRequest $request, bool $isRequired = false): self
    {
        /** @var ClarifyNaturalPersonBirthDetailsRequest $request */
        if (
            $isRequired &&
            $request->bornAt        === null &&
            ($request->placeOfBirth === null || \trim($request->placeOfBirth) === '')
        ) {
            $message = 'Данные о рождении не указаны.';
            $this->note->addError('bornAt', $message);
            $this->note->addError('placeOfBirth', $message);
        }

        return $this;
    }

    protected function validatePassport(ApplicationRequest $request): self
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

    protected function validateDeceasedDetails(ApplicationRequest $request): self
    {
        if ($request->bornAt !== null &&
            $request->diedAt !== null &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt) >
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt)
        ) {
            $this->note->addError('bornAt', 'Дата рождения не может следовать за датой смерти.');
        }
        if (
            $request->diedAt                       === null &&
            ($request->age                         !== null ||
            $request->causeOfDeathId               !== null ||
            $request->deathCertificateSeries       !== null ||
            $request->deathCertificateNumber       !== null ||
            $request->deathCertificateIssuedAt     !== null ||
            $request->cremationCertificateNumber   !== null ||
            $request->cremationCertificateIssuedAt !== null)
        ) {
            $this->note->addError('diedAt', 'Дата смерти не указана.');
        }
        if ($request->bornAt !== null &&
            $request->diedAt !== null &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt) <
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt)
        ) {
            $this->note->addError('diedAt', 'Дата смерти не может предшествовать дате рождения.');
        }
        if (
            $request->age    !== null &&
            $request->bornAt !== null &&
            $request->diedAt !== null
        ) {
            $this->note->addError('age', 'Возраст не может быть указан, т.к. уже указаны даты рождения и смерти.');
        }

        if (
            $request->deathCertificateSeries   !== null ||
            $request->deathCertificateNumber   !== null ||
            $request->deathCertificateIssuedAt !== null
        ) {
            if ($request->deathCertificateSeries === null) {
                $this->note->addError('deathCertificateSeries', 'Серия свидетельства о смерти не указана.');
            }
            if ($request->deathCertificateNumber === null) {
                $this->note->addError('deathCertificateNumber', 'Номер свидетельства о смерти не указан.');
            }
            if ($request->deathCertificateIssuedAt === null) {
                $this->note->addError('deathCertificateIssuedAt', 'Дата выдачи свидетельства о смерти не указана.');
            }
        }

        if (
            $request->cremationCertificateNumber   !== null ||
            $request->cremationCertificateIssuedAt !== null
        ) {
            if ($request->cremationCertificateNumber === null) {
                $this->note->addError('cremationCertificateNumber', 'Номер справки о смерти не указан.');
            }
            if ($request->cremationCertificateIssuedAt === null) {
                $this->note->addError('cremationCertificateIssuedAt', 'Дата выдачи справки о смерти не указана.');
            }
        }

        return $this;
    }
}
