<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RowInBlock
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
     * @param self $rowInBlock
     *
     * @return bool
     */
    public function isEqual(self $rowInBlock): bool
    {
        return $rowInBlock->value() === $this->value();
    }

    /**
     * @param int $rowInBlock
     */
    private function assertValidValue(int $rowInBlock): void
    {
        $this->assertNotNegative($rowInBlock);
        $this->assertNotZero($rowInBlock);
    }

    /**
     * @param int $rowInBlock
     *
     * @throws \InvalidArgumentException when the row in block value has negative value
     */
    private function assertNotNegative(int $rowInBlock): void
    {
        if ($rowInBlock < 0) {
            throw new \InvalidArgumentException('Ряд в квартале не может иметь отрицательное значение.');
        }
    }

    /**
     * @param int $rowInBlock
     *
     * @throws \InvalidArgumentException when the row in block value has zero value
     */
    private function assertNotZero(int $rowInBlock): void
    {
        if ($rowInBlock === 0) {
            throw new \InvalidArgumentException('Ряд в квартале не может иметь нулевое значение.');
        }
    }
}
