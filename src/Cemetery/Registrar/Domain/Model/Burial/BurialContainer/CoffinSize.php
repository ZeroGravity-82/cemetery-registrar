<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinSize
{
    private const MIN_SIZE = 165;
    private const MAX_SIZE = 225;

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
     * @param self $coffinSize
     *
     * @return bool
     */
    public function isEqual(self $coffinSize): bool
    {
        return $coffinSize->value() === $this->value();
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
