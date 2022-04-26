<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RowInColumbarium
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
     * @param int $rowInColumbarium
     */
    private function assertValidValue(int $rowInColumbarium): void
    {
        $this->assertNotNegative($rowInColumbarium);
        $this->assertNotZero($rowInColumbarium);
    }

    /**
     * @param int $rowInBlock
     *
     * @throws \InvalidArgumentException when the row in columbarium value has negative value
     */
    private function assertNotNegative(int $rowInBlock): void
    {
        if ($rowInBlock < 0) {
            throw new \InvalidArgumentException('Ряд в колумбарии не может иметь отрицательное значение.');
        }
    }

    /**
     * @param int $rowInBlock
     *
     * @throws \InvalidArgumentException when the row in columbarium value has zero value
     */
    private function assertNotZero(int $rowInBlock): void
    {
        if ($rowInBlock === 0) {
            throw new \InvalidArgumentException('Ряд в колумбарии не может иметь нулевое значение.');
        }
    }
}
