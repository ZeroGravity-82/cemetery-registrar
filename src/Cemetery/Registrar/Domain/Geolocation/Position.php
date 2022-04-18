<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Geolocation;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Position
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
        return \sprintf('%s [&plusmn; %sm]', $this->getCoordinates(), $this->getAccuracy());
    }

    /**
     * @return Coordinates
     */
    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return Accuracy|null
     */
    public function getAccuracy(): ?Accuracy
    {
        return $this->accuracy;
    }

    /**
     * @param self $position
     *
     * @return bool
     */
    public function isEqual(self $position): bool
    {
        $isSameCoordinates = $position->getCoordinates() === $this->getCoordinates();
        $isSameAccuracy    = $position->getAccuracy()    === $this->getAccuracy();

        return $isSameCoordinates && $isSameAccuracy;
    }
}
