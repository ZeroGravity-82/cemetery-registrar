<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Geolocation;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Coordinates
{
    private const COORDINATE_VALUE_PATTERN = '~^[+/\-]?\d+\.?\d+$~';    // examples: 54.950357, -165.1282, etc.
    private const ACCURACY_VALUE_PATTERN   = '~^\d+\.\d+$~';            // examples: 0.25, 12.5, etc.

    /**
     * @param string      $latitude
     * @param string      $longitude
     * @param string|null $accuracy
     */
    public function __construct(
        private string  $latitude,
        private string  $longitude,
        private ?string $accuracy = null,
    ) {
        $this->assertValidLatitude($this->latitude);
        $this->assertValidLongitude($this->longitude);
        if ($this->accuracy) {
            $this->assertValidAccuracy($this->accuracy);
        }
        $this->latitude  = \ltrim($this->latitude, '+');
        $this->longitude = \ltrim($this->longitude, '+');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('%s, %s', $this->latitude, $this->longitude);
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
     * @return string|null
     */
    public function getAccuracy(): ?string
    {
        return $this->accuracy;
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
        $isSameAccuracy  = $coordinates->getAccuracy()  === $this->getAccuracy();

        return $isSameLatitude && $isSameLongitude && $isSameAccuracy;
    }

    /**
     * @param string $latitude
     *
     * @throws \InvalidArgumentException when the latitude value is invalid
     */
    private function assertValidLatitude(string $latitude): void
    {
        $isValidFormat        = \preg_match(self::COORDINATE_VALUE_PATTERN, $latitude);
        $isInsideOfValidRange = (float) $latitude >= -90.0 && (float) $latitude <= 90.0;
        if (!$isValidFormat || !$isInsideOfValidRange) {
            throw new \InvalidArgumentException(\sprintf('Invalid latitude value "%s".', $latitude));
        }
    }

    /**
     * @param string $longitude
     *
     * @throws \InvalidArgumentException when the longitude value is invalid
     */
    private function assertValidLongitude(string $longitude): void
    {
        $isValidFormat        = \preg_match(self::COORDINATE_VALUE_PATTERN, $longitude);
        $isInsideOfValidRange = (float) $longitude >= -180.0 && (float) $longitude <= 180.0;
        if (!$isValidFormat || !$isInsideOfValidRange) {
            throw new \InvalidArgumentException(\sprintf('Invalid longitude value "%s".', $longitude));
        }
    }

    /**
     * @param string $accuracy
     *
     * @throws \InvalidArgumentException when the accuracy value is invalid
     */
    private function assertValidAccuracy(string $accuracy): void
    {
        $isValidFormat        = \preg_match(self::ACCURACY_VALUE_PATTERN, $accuracy);
        $isInsideOfValidRange = (float) $accuracy >= 0.0;
        if (!$isValidFormat || !$isInsideOfValidRange) {
            throw new \InvalidArgumentException(\sprintf('Invalid accuracy value "%s".', $accuracy));
        }
    }
}

