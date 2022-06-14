<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RowInColumbarium
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
     * @param self $rowInColumbarium
     *
     * @return bool
     */
    public function isEqual(self $rowInColumbarium): bool
    {
        return $rowInColumbarium->value() === $this->value();
    }

    /**
     * @param int $value
     */
    private function assertValidValue(int $value): void
    {
        $this->assertNotNegative($value);
        $this->assertNotZero($value);
    }

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the row in columbarium value has negative value
     */
    private function assertNotNegative(int $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Ряд в колумбарии не может иметь отрицательное значение.');
        }
    }

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the row in columbarium value has zero value
     */
    private function assertNotZero(int $value): void
    {
        if ($value === 0) {
            throw new \InvalidArgumentException('Ряд в колумбарии не может иметь нулевое значение.');
        }
    }
}
