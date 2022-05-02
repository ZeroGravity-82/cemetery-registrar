<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition;

use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class GeoPositionType extends JsonType
{
    private const TYPE_NAME = 'geo_position';

    /**
     * Registers type to the type map.
     */
    public static function registerType(): void
    {
        if (self::hasType(self::TYPE_NAME)) {
            return;
        }
        self::addType(self::TYPE_NAME, self::class);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if (!$value instanceof GeoPosition) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', GeoPosition::class]
            );
        }

        try {
            return \json_encode($this->prepareGeoPositionForEncoding($value), JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?GeoPosition
    {
        if ($value === null || $value instanceof GeoPosition) {
            return $value;
        }

        try {
            $decodedValue = \json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            $this->assertValid($decodedValue, $value);

            return $this->buildGeoPosition($decodedValue);
        } catch (\JsonException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    /**
     * @param mixed $decodedValue
     * @param mixed $value
     *
     * @throws \RuntimeException when the decoded value has invalid format.
     */
    private function assertValid(mixed $decodedValue, mixed $value): void
    {
        $isInvalidValue =
            !\array_key_exists('coordinates', $decodedValue)                ||
            !\array_key_exists('latitude',    $decodedValue['coordinates']) ||
            !\array_key_exists('longitude',   $decodedValue['coordinates']) ||
            !\array_key_exists('accuracy',    $decodedValue);
        if ($isInvalidValue) {
            throw new \RuntimeException(\sprintf('Неверный формат геопозиции: "%s".', $value));
        }
    }

    /**
     * @param GeoPosition $geoPosition
     *
     * @return array
     */
    private function prepareGeoPositionForEncoding(GeoPosition $geoPosition): array
    {
        return [
            'coordinates' => [
                'latitude'  => (float) $geoPosition->coordinates()->latitude(),
                'longitude' => (float) $geoPosition->coordinates()->longitude(),
            ],
            'accuracy' => !\is_null($geoPosition->accuracy())
                ? (float) $geoPosition->accuracy()->value()
                : null
        ];
    }

    /**
     * @param array $decodedValue
     *
     * @return GeoPosition
     */
    private function buildGeoPosition(array $decodedValue): GeoPosition
    {
        return new GeoPosition(
            new Coordinates(
                (string) $decodedValue['coordinates']['latitude'],
                (string) $decodedValue['coordinates']['longitude']
            ),
            !\is_null($decodedValue['accuracy']) ? new Accuracy((string) $decodedValue['accuracy']) : null
        );
    }
}
