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
     * @param self $position
     *
     * @return bool
     */
    public function isEqual(self $position): bool
    {
        $isSameCoordinates = $position->coordinates() === $this->coordinates();
        $isSameAccuracy    = $position->accuracy()    === $this->accuracy();

        return $isSameCoordinates && $isSameAccuracy;
    }
}
