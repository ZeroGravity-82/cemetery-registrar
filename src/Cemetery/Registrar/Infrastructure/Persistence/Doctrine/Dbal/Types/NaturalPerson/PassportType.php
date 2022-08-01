<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\Passport;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PassportType extends CustomJsonType
{
    protected string $className = Passport::class;
    protected string $typeName  = 'passport';

    /**
     * @throws \UnexpectedValueException when the decoded value has invalid format
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\is_array($decodedValue)                         ||
            !\array_key_exists('series',       $decodedValue) ||
            !\array_key_exists('number',       $decodedValue) ||
            !\array_key_exists('issuedAt',     $decodedValue) ||
            !\array_key_exists('issuedBy',     $decodedValue) ||
            !\array_key_exists('divisionCode', $decodedValue);
        if ($isInvalidValue) {
            throw new \UnexpectedValueException(\sprintf(
                'Неверный формат декодированного значения для паспортных данных: "%s".',
                $value,
            ));
        }
    }

    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        /** @var Passport $value */
        return [
            'series'       => $value->series(),
            'number'       => $value->number(),
            'issuedAt'     => $value->issuedAt()->format('Y-m-d'),
            'issuedBy'     => $value->issuedBy(),
            'divisionCode' => $value->divisionCode(),
        ];
    }

    /**
     * @throws \UnexpectedValueException when the issue date has invalid format
     * @throws Exception                 when the series is invalid
     * @throws Exception                 when the number is invalid
     * @throws Exception                 when the issuing authority name is invalid
     * @throws Exception                 when the division code is invalid (if any)
     */
    protected function buildPhpValue(array $decodedValue): Passport
    {
        $passportIssuedAt = \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['issuedAt']);
        if ($passportIssuedAt === false) {
            throw new \UnexpectedValueException(\sprintf(
                'Неверный формат декодированного значения для даты выдачи паспорта: "%s".',
                $decodedValue['issuedAt'],
            ));
        }

        return new Passport(
            $decodedValue['series'],
            $decodedValue['number'],
            $passportIssuedAt,
            $decodedValue['issuedBy'],
            $decodedValue['divisionCode'],
        );
    }
}
