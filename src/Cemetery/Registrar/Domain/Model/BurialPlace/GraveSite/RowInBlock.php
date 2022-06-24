<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RowInBlock
{
    private const MAX_ROW = 100;

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
     * @param self $rowInBlock
     *
     * @return bool
     */
    public function isEqual(self $rowInBlock): bool
    {
        return $rowInBlock->value() === $this->value();
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
     * @throws \InvalidArgumentException when the row in block value has negative value
     */
    private function assertNotNegative(int $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Ряд в квартале не может иметь отрицательное значение.');
        }
    }

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the row in block value has zero value
     */
    private function assertNotZero(int $value): void
    {
        if ($value === 0) {
            throw new \InvalidArgumentException('Ряд в квартале не может иметь нулевое значение.');
        }
    }

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the row in block has too much value
     */
    private function assertNotTooMuch(int $value): void
    {
        if ($value > self::MAX_ROW) {
            throw new \InvalidArgumentException(\sprintf('Ряд в квартале не может иметь значение больше %d.', self::MAX_ROW));
        }
    }
}
