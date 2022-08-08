<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\GeoPosition;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Coordinates
{
    public const FORMAT = '~^[+|\-]?\d+(?:\.\d+)?$~';    // examples: 54.950357, 0, -165.1282, 90, etc.

    private string $latitude;
    private string $longitude;

    /**
     * @throws Exception when the latitude value is empty
     * @throws Exception when the latitude value has an invalid format
     * @throws Exception when the latitude value is out of valid range
     * @throws Exception when the longitude value is empty
     * @throws Exception when the longitude value has an invalid format
     * @throws Exception when the longitude value is out of valid range
     */
    public function __construct(
        string $latitude,
        string $longitude,
    ) {
        $this->assertValidLatitudeValue($latitude);
        $this->assertValidLongitudeValue($longitude);
        $this->latitude  = $this->format($latitude);
        $this->longitude = $this->format($longitude);
    }

    public function __toString(): string
    {
        return \sprintf('%s, %s', $this->latitude(), $this->longitude());
    }

    public function latitude(): string
    {
        return $this->format($this->latitude);
    }

    public function longitude(): string
    {
        return $this->format($this->longitude);
    }

    public function isEqual(self $coordinates): bool
    {
        $isSameLatitude  = $this->format($coordinates->latitude())  === $this->latitude();
        $isSameLongitude = $this->format($coordinates->longitude()) === $this->longitude();

        return $isSameLatitude && $isSameLongitude;
    }

    public static function isValidFormat(string $value): bool
    {
        return \preg_match(self::FORMAT, $value) === 1;
    }

    /**
     * @throws Exception when the latitude value is empty
     * @throws Exception when the latitude value has an invalid format
     * @throws Exception when the latitude value is out of valid range
     */
    private function assertValidLatitudeValue(string $latitude): void
    {
        $this->assertNotEmpty($latitude, 'Широта');
        $this->assertValidFormat($latitude, 'широты');
        $this->assertIsInTheValidRange($latitude, -90.0, 90.0, 'Широта');
    }

    /**
     * @throws Exception when the longitude value is empty
     * @throws Exception when the longitude value has an invalid format
     * @throws Exception when the longitude value is out of valid range
     */
    private function assertValidLongitudeValue(string $longitude): void
    {
        $this->assertNotEmpty($longitude, 'Долгота');
        $this->assertValidFormat($longitude, 'долготы');
        $this->assertIsInTheValidRange($longitude, -180.0, 180.0, 'Долгота');
    }

    /**
     * @throws Exception when the value is empty
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if (\trim($value) === '') {
            throw new Exception(\sprintf('%s не может иметь пустое значение.', $name));
        }
    }

    /**
     * @throws Exception when the value has an invalid format
     */
    private function assertValidFormat(string $value, string $name): void
    {
        if (!\preg_match(self::FORMAT, $value)) {
            throw new Exception(\sprintf('Неверный формат %s.', $name));
        }
    }

    /**
     * @throws Exception when the value is out of valid range
     */
    private function assertIsInTheValidRange(string $value, float $min, float $max, string $name): void
    {
        if ((float) $value < $min || (float) $value > $max) {
            throw new Exception(\sprintf(
                '%s "%s" находится вне допустимого диапазона [%.0f, %.0f].',
                $name,
                $value,
                $min,
                $max,
            ));
        }
    }

    private function format(string $value): string
    {
        $value = $this->addDecimalPoint($value);
        $value = $this->trimPrecedingZeros($value);
        $value = $this->trimTrailingZeros($value);

        return $this->removePlusSign($value);
    }

    private function addDecimalPoint(string $value): string
    {
        if (!\str_contains($value, '.')) {
            $value .= '.0';
        }

        return $value;
    }

    private function trimPrecedingZeros(string $value): string
    {
        $plusSignFound  = false;
        $minusSignFound = false;
        if (\str_starts_with($value, '+')) {
            $plusSignFound = true;
            $value         = \ltrim($value, '+');
        }
        if (\str_starts_with($value, '-')) {
            $minusSignFound = true;
            $value          = \ltrim($value, '-');
        }
        $value = \ltrim($value, '0');
        if (\str_starts_with($value, '.')) {
            $value = '0' . $value;
        }
        if ($plusSignFound) {
            $value = '+' . $value;
        }
        if ($minusSignFound) {
            $value = '-' . $value;
        }

        return $value;
    }

    private function trimTrailingZeros(string $value): string
    {
        $value = \rtrim($value, '0');
        if (\str_ends_with($value, '.')) {
            $value = $value . '0';
        }

        return $value;
    }

    private function removePlusSign(string $value): string
    {
        return \ltrim($value, '+');
    }
}
