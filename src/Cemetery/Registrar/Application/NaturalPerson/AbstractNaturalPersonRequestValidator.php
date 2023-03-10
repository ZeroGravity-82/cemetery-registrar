<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;
use Cemetery\Registrar\Domain\Model\Contact\Email;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractNaturalPersonRequestValidator extends AbstractApplicationRequestValidator
{
    public function __construct(
        private NaturalPersonFetcherInterface $naturalPersonFetcher,
    ) {
        parent::__construct();
    }

    protected function validateUniquenessConstraints(AbstractApplicationRequest $request): self
    {
        // TODO implement

        return $this;
    }

    protected function validateFullName(AbstractApplicationRequest $request): self
    {
        if ($request->fullName === null || empty(\trim($request->fullName))) {
            $this->note->addError('fullName', 'ФИО не может иметь пустое значение.');
        }

        return $this;
    }

    protected function validateContact(AbstractApplicationRequest $request, bool $isRequired = false): self
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

    protected function validatePassport(AbstractApplicationRequest $request, bool $isRequired = false): self
    {
        $now                     = new \DateTimeImmutable();
        $passportSeriesMessage   = 'Серия паспорта не указана.';
        $passportNumberMessage   = 'Номер паспорта не указан.';
        $passportIssuedAtMessage = 'Дата выдачи паспорта не указана.';
        $passportIssuedByMessage = 'Орган, выдавший паспорт, не указан.';

        if ($request->passportIssuedAt !== null && !$this->doesDateTimeStringHasValidFormat($request->passportIssuedAt)) {
            $this->note->addError('passportIssuedAt', 'Неверный формат даты выдачи паспорта.');
        }

        if (
            $request->passportIssuedAt !== null                                 &&
            $this->doesDateTimeStringHasValidFormat($request->passportIssuedAt) &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->passportIssuedAt) > $now
        ) {
            $this->note->addError('passportIssuedAt', 'Дата выдачи паспорта не может иметь значение из будущего.');
        }

        if (
            $isRequired &&
            ($request->passportSeries   === null || \trim($request->passportSeries)   === '') &&
            ($request->passportNumber   === null || \trim($request->passportNumber)   === '') &&
            ($request->passportIssuedAt === null || \trim($request->passportIssuedAt) === '') &&
            ($request->passportIssuedBy === null || \trim($request->passportIssuedBy) === '')
        ) {
            $this->note->addError('passportSeries',   $passportSeriesMessage);
            $this->note->addError('passportNumber',   $passportNumberMessage);
            $this->note->addError('passportIssuedAt', $passportIssuedAtMessage);
            $this->note->addError('passportIssuedBy', $passportIssuedByMessage);
        } elseif (
            $request->passportSeries       !== null ||
            $request->passportNumber       !== null ||
            $request->passportIssuedAt     !== null ||
            $request->passportIssuedBy     !== null ||
            $request->passportDivisionCode !== null
        ) {
            if ($request->passportSeries === null) {
                $this->note->addError('passportSeries', $passportSeriesMessage);
            }
            if ($request->passportNumber === null) {
                $this->note->addError('passportNumber', $passportNumberMessage);
            }
            if ($request->passportIssuedAt === null) {
                $this->note->addError('passportIssuedAt', $passportIssuedAtMessage);
            }
            if ($request->passportIssuedBy === null) {
                $this->note->addError('passportIssuedBy', $passportIssuedByMessage);
            }
        }

        return $this;
    }

    protected function validateBirthDetails(AbstractApplicationRequest $request, bool $isRequired = false): self
    {
        $now = new \DateTimeImmutable();
        if (
            $isRequired                      &&
            $request->bornAt        === null &&
            ($request->placeOfBirth === null || \trim($request->placeOfBirth) === '')
        ) {
            $message = 'Данные о рождении не указаны.';
            $this->note->addError('bornAt', $message);
            $this->note->addError('placeOfBirth', $message);
        }

        if ($request->bornAt !== null && !$this->doesDateTimeStringHasValidFormat($request->bornAt)) {
            $this->note->addError('bornAt', 'Неверный формат даты рождения.');
        }
        if ($request->diedAt !== null && !$this->doesDateTimeStringHasValidFormat($request->diedAt)) {
            $this->note->addError('diedAt', 'Неверный формат даты смерти.');
        }

        if (
            $request->bornAt !== null                                 &&
            $this->doesDateTimeStringHasValidFormat($request->bornAt) &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt) > $now
        ) {
            $this->note->addError('bornAt', 'Дата рождения не может иметь значение из будущего.');
        } elseif (
            $request->bornAt !== null                                 &&
            $request->diedAt !== null                                 &&
            $this->doesDateTimeStringHasValidFormat($request->bornAt) &&
            $this->doesDateTimeStringHasValidFormat($request->diedAt) &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt) >
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt)
        ) {
            $this->note->addError('bornAt', 'Дата рождения не может следовать за датой смерти.');
        } else {
            $this->validateAgeMatchingBornAtAndDiedAt($request);
        }

        return $this;
    }

    protected function validateDeceasedDetails(AbstractApplicationRequest $request, bool $isRequired = false): self
    {
        $now = new \DateTimeImmutable();
        if (
            $request->diedAt                       === null &&
            ($isRequired                                    ||
            $request->age                          !== null ||
            $request->causeOfDeathId               !== null ||
            $request->deathCertificateSeries       !== null ||
            $request->deathCertificateNumber       !== null ||
            $request->deathCertificateIssuedAt     !== null ||
            $request->cremationCertificateNumber   !== null ||
            $request->cremationCertificateIssuedAt !== null)
        ) {
            $this->note->addError('diedAt', 'Дата смерти не указана.');
        }

        if ($request->bornAt !== null && !$this->doesDateTimeStringHasValidFormat($request->bornAt)) {
            $this->note->addError('bornAt', 'Неверный формат даты рождения.');
        }
        if ($request->diedAt !== null && !$this->doesDateTimeStringHasValidFormat($request->diedAt)) {
            $this->note->addError('diedAt', 'Неверный формат даты смерти.');
        }
        if ($request->deathCertificateIssuedAt !== null && !$this->doesDateTimeStringHasValidFormat($request->deathCertificateIssuedAt)) {
            $this->note->addError('deathCertificateIssuedAt', 'Неверный формат даты выдачи свидетельства о смерти.');
        }
        if ($request->deathCertificateIssuedAt !== null && !$this->doesDateTimeStringHasValidFormat($request->cremationCertificateIssuedAt)) {
            $this->note->addError('cremationCertificateIssuedAt', 'Неверный формат даты выдачи справки о смерти.');
        }

        if (
            $request->diedAt !== null                                 &&
            $this->doesDateTimeStringHasValidFormat($request->diedAt) &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt) > $now
        ) {
            $this->note->addError('diedAt', 'Дата смерти не может иметь значение из будущего.');
        } elseif (
            $request->bornAt !== null                                 &&
            $request->diedAt !== null                                 &&
            $this->doesDateTimeStringHasValidFormat($request->bornAt) &&
            $this->doesDateTimeStringHasValidFormat($request->diedAt) &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt) <
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt)
        ) {
            $this->note->addError('diedAt', 'Дата смерти не может предшествовать дате рождения.');
        } else {
            $this->validateAgeMatchingBornAtAndDiedAt($request);
        }

        if (
            $request->deathCertificateIssuedAt !== null                                 &&
            $this->doesDateTimeStringHasValidFormat($request->deathCertificateIssuedAt) &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->deathCertificateIssuedAt) > $now
        ) {
            $this->note->addError('deathCertificateIssuedAt', 'Дата выдачи свидетельства о смерти не может иметь значение из будущего.');
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
            $request->cremationCertificateIssuedAt !== null                                 &&
            $this->doesDateTimeStringHasValidFormat($request->cremationCertificateIssuedAt) &&
            \DateTimeImmutable::createFromFormat('Y-m-d', $request->cremationCertificateIssuedAt) > $now
        ) {
            $this->note->addError('cremationCertificateIssuedAt', 'Дата выдачи справки о смерти не может иметь значение из будущего.');
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

    private function validateAgeMatchingBornAtAndDiedAt(AbstractApplicationRequest $request)
    {
        if (
            $request->bornAt !== null &&
            $request->diedAt !== null &&
            $request->age    !== null &&
            $request->age    !== \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt)->diff(
                                 \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt))->y
        ) {
            $message = 'Возраст не соответствует датам рождения и смерти.';
            $this->note->addError('bornAt', $message);
            $this->note->addError('diedAt', $message);
            $this->note->addError('age',    $message);
        }
    }
}
