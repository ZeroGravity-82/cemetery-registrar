<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\GeoPosition;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GeoPositionType extends CustomJsonType
{
    protected string $className = GeoPosition::class;
    protected string $typeName  = 'geo_position';

    /**
     * @throws \LogicException when the geo position decoded value is invalid
     */
    protected function assertValidDecodedValue(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\array_key_exists('coordinates', $decodedValue)                ||
            !\array_key_exists('latitude',    $decodedValue['coordinates']) ||
            !\array_key_exists('longitude',   $decodedValue['coordinates']) ||
            !\array_key_exists('error',       $decodedValue);
        if ($isInvalidValue) {
            throw new \LogicException(\sprintf('Неверный формат геопозиции: "%s".', $value));
        }
    }

    protected function preparePhpValueForJsonEncoding(mixed $value): array
    {
        /** @var GeoPosition $value */
        return [
            'coordinates' => [
                'latitude'  => (float) $value->coordinates()->latitude(),
                'longitude' => (float) $value->coordinates()->longitude(),
            ],
            'error' => !\is_null($value->error())
                ? (float) $value->error()->value()
                : null,
        ];
    }

    /**
     * @throws Exception when the latitude decoded value is invalid
     * @throws Exception when the longitude decoded value is invalid
     * @throws Exception when the error decoded value is invalid
     */
    protected function buildPhpValue(array $decodedValue): GeoPosition
    {
        return new GeoPosition(
            new Coordinates(
                (string) $decodedValue['coordinates']['latitude'],
                (string) $decodedValue['coordinates']['longitude']
            ),
            !\is_null($decodedValue['error']) ? new Error((string) $decodedValue['error']) : null,
        );
    }
}
