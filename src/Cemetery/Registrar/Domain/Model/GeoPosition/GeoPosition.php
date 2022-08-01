<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GeoPosition
{
    public function __construct(
        private Coordinates $coordinates,
        private ?Error      $error,
    ) {}

    public function __toString(): string
    {
        return \sprintf('%s [&plusmn; %sm]', $this->coordinates(), $this->error());
    }

    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function error(): ?Error
    {
        return $this->error;
    }

    public function isEqual(self $geoPosition): bool
    {
        $isSameCoordinates = $geoPosition->coordinates()->isEqual($this->coordinates());
        $isSameError       = $this->hasSameError($geoPosition);

        return $isSameCoordinates && $isSameError;
    }

    private function hasSameError(self $geoPosition): bool
    {
        $isSame = false;

        if ($geoPosition->error() !== null && $this->error() !== null) {
            $isSame = $geoPosition->error()->isEqual($this->error());
        } elseif ($geoPosition->error() === null && $this->error() === null) {
            $isSame = true;
        }

        return $isSame;
    }
}
