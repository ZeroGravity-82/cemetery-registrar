<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PositionInRow
{
    private const MAX_POSITION = 100;

    /**
     * @throws Exception when the position in row value has negative value
     * @throws Exception when the position in row value has zero value
     * @throws Exception when the position in row has too much value
     */
    public function __construct(
        private int $value,
    ) {
        $this->assertValidValue($value);
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isEqual(self $positionInRow): bool
    {
        return $positionInRow->value() === $this->value();
    }

    /**
     * @throws Exception when the position in row value has negative value
     * @throws Exception when the position in row value has zero value
     * @throws Exception when the position in row has too much value
     */
    private function assertValidValue(int $value): void
    {
        $this->assertNotNegative($value);
        $this->assertNotZero($value);
        $this->assertNotTooMuch($value);
    }

    /**
     * @throws Exception when the position in row value has negative value
     */
    private function assertNotNegative(int $value): void
    {
        if ($value < 0) {
            throw new Exception('Место в ряду не может иметь отрицательное значение.');
        }
    }

    /**
     * @throws Exception when the position in row value has zero value
     */
    private function assertNotZero(int $positionInRow): void
    {
        if ($positionInRow === 0) {
            throw new Exception('Место в ряду не может иметь нулевое значение.');
        }
    }

    /**
     * @throws Exception when the position in row has too much value
     */
    private function assertNotTooMuch(int $value): void
    {
        if ($value > self::MAX_POSITION) {
            throw new Exception(\sprintf('Место в ряду не может иметь значение больше %d.', self::MAX_POSITION));
        }
    }
}
