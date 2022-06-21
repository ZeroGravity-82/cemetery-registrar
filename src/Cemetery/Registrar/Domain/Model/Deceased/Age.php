<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Deceased;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Age
{
    private const MAX_AGE = 125;

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
     * @param self $type
     *
     * @return bool
     */
    public function isEqual(self $type): bool
    {
        return $type->value() === $this->value();
    }

    /**
     * @param int $value
     */
    private function assertValidValue(int $value): void
    {
        $this->assertNotNegative($value);
        $this->assertNotTooMuch($value);
    }

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the age is negative
     */
    private function assertNotNegative(int $value): void
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Возраст не может иметь отрицательное значение.');
        }
    }

    /**
     * @param int $value
     *
     * @throws \InvalidArgumentException when the age has too much value
     */
    private function assertNotTooMuch(int $value): void
    {
        if ($value > self::MAX_AGE) {
            throw new \InvalidArgumentException(\sprintf('Возраст не может превышать %d лет.', self::MAX_AGE));
        }
    }
}
