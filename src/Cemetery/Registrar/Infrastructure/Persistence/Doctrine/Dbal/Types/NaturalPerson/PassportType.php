<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\Passport;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PassportType extends CustomJsonType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Passport::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'passport';

    /**
     * {@inheritdoc}
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\array_key_exists('series',       $decodedValue) ||
            !\array_key_exists('number',       $decodedValue) ||
            !\array_key_exists('issuedAt',     $decodedValue) ||
            !\array_key_exists('issuedBy',     $decodedValue) ||
            !\array_key_exists('divisionCode', $decodedValue);
        if ($isInvalidValue) {
            throw new \RuntimeException(\sprintf('Неверный формат паспортных данных: "%s".', $value));
        }
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): Passport
    {
        return new Passport(
            $decodedValue['series'],
            $decodedValue['number'],
            \DateTimeImmutable::createFromFormat('Y-m-d', $decodedValue['issuedAt']),
            $decodedValue['issuedBy'],
            $decodedValue['divisionCode'],
        );
    }
}
