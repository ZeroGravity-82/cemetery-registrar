<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RowInColumbarium
{
    /**
     * @throws Exception when the row in columbarium value has negative value
     * @throws Exception when the row in columbarium value has zero value
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

    public function isEqual(self $rowInColumbarium): bool
    {
        return $rowInColumbarium->value() === $this->value();
    }

    /**
     * @throws Exception when the row in columbarium value has negative value
     * @throws Exception when the row in columbarium value has zero value
     */
    private function assertValidValue(int $value): void
    {
        $this->assertNotNegative($value);
        $this->assertNotZero($value);
    }

    /**
     * @throws Exception when the row in columbarium value has negative value
     */
    private function assertNotNegative(int $value): void
    {
        if ($value < 0) {
            throw new Exception('Ряд в колумбарии не может иметь отрицательное значение.');
        }
    }

    /**
     * @throws Exception when the row in columbarium value has zero value
     */
    private function assertNotZero(int $value): void
    {
        if ($value === 0) {
            throw new Exception('Ряд в колумбарии не может иметь нулевое значение.');
        }
    }
}
