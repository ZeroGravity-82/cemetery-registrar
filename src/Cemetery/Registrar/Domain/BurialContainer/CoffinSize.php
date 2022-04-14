<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CoffinSize
{
    private const MIN_SIZE = 165;
    private const MAX_SIZE = 225;

    /**
     * @param int $value
     */
    public function __construct(
        private int $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getValue();
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param self $coffinSize
     *
     * @return bool
     */
    public function isEqual(self $coffinSize): bool
    {
        return $coffinSize->getValue() === $this->getValue();
    }

    /**
     * @param int $value
     */
    private function assertValidValue(int $value): void
    {
        $this->assertIsInTheValidRange($value);
    }

    /**
     * @param int $value
     *
     * @throws \RuntimeException when the value is out of valid range
     */
    private function assertIsInTheValidRange(int $value): void
    {
        if ($value < self::MIN_SIZE || $value > self::MAX_SIZE) {
            throw new \RuntimeException(\sprintf(
                'Размер гроба %d см находится вне допустимого диапазона [%d, %d] см.',
                $value,
                self::MIN_SIZE,
                self::MAX_SIZE
            ));
        }
    }
}