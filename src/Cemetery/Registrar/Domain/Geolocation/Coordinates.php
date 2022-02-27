<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Geolocation;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Coordinates
{
    private const VALUE_PATTERN = '~^[+/\-]?\d+\.?\d+$~';    // examples: 54.950357, -165.1282, etc.

    /**
     * @param string $latitude
     * @param string $longitude
     */
    public function __construct(
        private string $latitude,
        private string $longitude,
    ) {
        $this->assertValidLatitudeValue($latitude);
        $this->assertValidLongitudeValue($longitude);
        $this->latitude  = \ltrim($latitude, '+');
        $this->longitude = \ltrim($longitude, '+');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('%s, %s', $this->getLatitude(), $this->getLongitude());
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @param self $coordinates
     *
     * @return bool
     */
    public function isEqual(self $coordinates): bool
    {
        $isSameLatitude  = $coordinates->getLatitude()  === $this->getLatitude();
        $isSameLongitude = $coordinates->getLongitude() === $this->getLongitude();

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
}
