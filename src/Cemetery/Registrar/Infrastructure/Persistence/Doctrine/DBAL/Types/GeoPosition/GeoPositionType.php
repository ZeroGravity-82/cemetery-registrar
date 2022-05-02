<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition;

use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\Error;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class GeoPositionType extends CustomJsonType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = GeoPosition::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'geo_position';

    /**
     * {@inheritdoc}
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\array_key_exists('coordinates', $decodedValue)                ||
            !\array_key_exists('latitude',    $decodedValue['coordinates']) ||
            !\array_key_exists('longitude',   $decodedValue['coordinates']) ||
            !\array_key_exists('error',       $decodedValue);
        if ($isInvalidValue) {
            throw new \RuntimeException(\sprintf('Неверный формат геопозиции: "%s".', $value));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        return [
            'coordinates' => [
                'latitude'  => (float) $value->coordinates()->latitude(),
                'longitude' => (float) $value->coordinates()->longitude(),
            ],
            'error' => !\is_null($value->error())
                ? (float) $value->error()->value()
                : null
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function buildPhpValue(array $decodedValue): GeoPosition
    {
        return new GeoPosition(
            new Coordinates(
                (string) $decodedValue['coordinates']['latitude'],
                (string) $decodedValue['coordinates']['longitude']
            ),
            !\is_null($decodedValue['error']) ? new Error((string) $decodedValue['error']) : null
        );
    }
}
