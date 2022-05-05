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
     * @param Error|null    $error
     */
    public function __construct(
        private readonly Coordinates $coordinates,
        private readonly ?Error      $error,
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('%s [&plusmn; %sm]', $this->coordinates(), $this->error());
    }

    /**
     * @return Coordinates
     */
    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return Error|null
     */
    public function error(): ?Error
    {
        return $this->error;
    }

    /**
     * @param self $geoPosition
     *
     * @return bool
     */
    public function isEqual(self $geoPosition): bool
    {
        $isSameCoordinates = $geoPosition->coordinates()->isEqual($this->coordinates());
        $isSameError       = $this->hasSameError($geoPosition);

        return $isSameCoordinates && $isSameError;
    }

    /**
     * @param self $geoPosition
     *
     * @return bool
     */
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
