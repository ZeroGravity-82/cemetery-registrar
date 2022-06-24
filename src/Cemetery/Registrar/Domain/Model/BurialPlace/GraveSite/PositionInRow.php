<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PositionInRow
{
    private const MAX_POSITION = 100;

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
     * @param int $value
     */
    private function assertValidValue(int $value): void
    {
        $this->assertNotNegative($value);
        $this->assertNotZero($value);
        $this->assertNotTooMuch($value);
    }

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the position in row value has negative value
     */
    private function assertNotNegative(int $value): void
    {
        if ($value < 0) {
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

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the position in row has too much value
     */
    private function assertNotTooMuch(int $value): void
    {
        if ($value > self::MAX_POSITION) {
            throw new \InvalidArgumentException(\sprintf('Место в ряду не может иметь значение больше %d.', self::MAX_POSITION));
        }
    }
}
