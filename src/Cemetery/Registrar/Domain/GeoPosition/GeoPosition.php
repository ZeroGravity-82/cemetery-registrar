<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class GeoPosition
{
    /**
     * @param Coordinates   $coordinates
     * @param Accuracy|null $accuracy
     */
    public function __construct(
        private readonly Coordinates $coordinates,
        private readonly ?Accuracy   $accuracy = null,
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('%s [&plusmn; %sm]', $this->coordinates(), $this->accuracy());
    }

    /**
     * @return Coordinates
     */
    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return Accuracy|null
     */
    public function accuracy(): ?Accuracy
    {
        return $this->accuracy;
    }

    /**
     * @param self $geoPosition
     *
     * @return bool
     */
    public function isEqual(self $geoPosition): bool
    {
        $isSameCoordinates = $geoPosition->coordinates()->isEqual($this->coordinates());
        $isSameAccuracy    = $this->checkSameAccuracy($geoPosition);

        return $isSameCoordinates && $isSameAccuracy;
    }

    /**
     * @param self $geoPosition
     *
     * @return bool
     */
    private function checkSameAccuracy(self $geoPosition): bool
    {
        $isSame = false;

        if ($geoPosition->accuracy() !== null && $this->accuracy() !== null) {
            $isSame = $geoPosition->accuracy()->isEqual($this->accuracy());
        } elseif ($geoPosition->accuracy() === null && $this->accuracy() === null) {
            $isSame = true;
        }

        return $isSame;
    }
}
