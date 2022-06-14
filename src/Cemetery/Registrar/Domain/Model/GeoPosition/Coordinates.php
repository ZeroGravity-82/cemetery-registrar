<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Coordinates
{
    public const VALUE_PATTERN = '~^[+|\-]?\d+(?:\.\d+)?$~';    // examples: 54.950357, 0, -165.1282, 90, etc.

    /**
     * @var string
     */
    private readonly string $latitude;

    /**
     * @var string
     */
    private readonly string $longitude;

    /**
     * @param string $latitude
     * @param string $longitude
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('%s, %s', $this->latitude(), $this->longitude());
    }

    /**
     * @return string
     */
    public function latitude(): string
    {
        return $this->format($this->latitude);
    }

    /**
     * @return string
     */
    public function longitude(): string
    {
        return $this->format($this->longitude);
    }

    /**
     * @param self $coordinates
     *
     * @return bool
     */
    public function isEqual(self $coordinates): bool
    {
        $isSameLatitude  = $this->format($coordinates->latitude())  === $this->latitude();
        $isSameLongitude = $this->format($coordinates->longitude()) === $this->longitude();

        return $isSameLatitude && $isSameLongitude;
    }

    /**
     * @param string $latitude
     */
    private function assertValidLatitudeValue(string $latitude): void
    {
        $name = 'Широта';
        $this->assertNotEmpty($latitude, $name);
        $this->assertValidFormat($latitude, $name);
        $this->assertIsInTheValidRange($latitude, -90.0, 90.0, $name);
    }

    /**
     * @param string $longitude
     */
    private function assertValidLongitudeValue(string $longitude): void
    {
        $name = 'Долгота';
        $this->assertNotEmpty($longitude, $name);
        $this->assertValidFormat($longitude, $name);
        $this->assertIsInTheValidRange($longitude, -180.0, 180.0, $name);
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws \InvalidArgumentException when the value is empty
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException(\sprintf('%s не может иметь пустое значение.', $name));
        }
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws \InvalidArgumentException when the value has an invalid format
     */
    private function assertValidFormat(string $value, string $name): void
    {
        if (!\preg_match(self::VALUE_PATTERN, $value)) {
            throw new \InvalidArgumentException(\sprintf('%s "%s" имеет неверный формат.', $name, $value));
        }
    }

    /**
     * @param string $value
     * @param float  $min
     * @param float  $max
     * @param string $name
     *
     * @throws \InvalidArgumentException when the value is out of valid range
     */
    private function assertIsInTheValidRange(string $value, float $min, float $max, string $name): void
    {
        if ((float) $value < $min || (float) $value > $max) {
            throw new \InvalidArgumentException(\sprintf(
                '%s "%s" находится вне допустимого диапазона [%.0f, %.0f].',
                $name,
                $value,
                $min,
                $max,
            ));
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function format(string $value): string
    {
        $value = $this->addDecimalPoint($value);
        $value = $this->trimPrecedingZeros($value);
        $value = $this->trimTrailingZeros($value);

        return $this->removePlusSign($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function addDecimalPoint(string $value): string
    {
        if (!\str_contains($value, '.')) {
            $value .= '.0';
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
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

    /**
     * @param string $value
     *
     * @return string
     */
    private function trimTrailingZeros(string $value): string
    {
        $value = \rtrim($value, '0');
        if (\str_ends_with($value, '.')) {
            $value = $value . '0';
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function removePlusSign(string $value): string
    {
        return \ltrim($value, '+');
    }
}
