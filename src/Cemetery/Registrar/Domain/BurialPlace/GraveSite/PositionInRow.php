<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class PositionInRow
{
    /**
     * @param int $value
     */
    public function __construct(
        private readonly int $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value();
    }

    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * @param self $positionInRow
     *
     * @return bool
     */
    public function isEqual(self $positionInRow): bool
    {
        return $positionInRow->value() === $this->value();
    }

    /**
     * @param int $positionInRow
     */
    private function assertValidValue(int $positionInRow): void
    {
        $this->assertNotNegative($positionInRow);
        $this->assertNotZero($positionInRow);
    }

    /**
     * @param int $positionInRow
     *
     * @throws \InvalidArgumentException when the position in row value has negative value
     */
    private function assertNotNegative(int $positionInRow): void
    {
        if ($positionInRow < 0) {
            throw new \InvalidArgumentException('Место в ряду не может иметь отрицательное значение.');
        }
    }

    /**
     * @param int $positionInRow
     *
     * @throws \InvalidArgumentException when the position in row value has zero value
     */
    private function assertNotZero(int $positionInRow): void
    {
        if ($positionInRow === 0) {
            throw new \InvalidArgumentException('Место в ряду не может иметь нулевое значение.');
        }
    }
}
