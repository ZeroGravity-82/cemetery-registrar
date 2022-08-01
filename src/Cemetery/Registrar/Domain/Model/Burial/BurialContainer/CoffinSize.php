<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinSize
{
    private const MIN_SIZE = 165;
    private const MAX_SIZE = 225;

    /**
     * @throws Exception when the value is out of valid range
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

    public function isEqual(self $coffinSize): bool
    {
        return $coffinSize->value() === $this->value();
    }

    /**
     * @throws Exception when the value is out of valid range
     */
    private function assertValidValue(int $value): void
    {
        $this->assertIsInTheValidRange($value);
    }

    /**
     * @throws Exception when the value is out of valid range
     */
    private function assertIsInTheValidRange(int $value): void
    {
        if ($value < self::MIN_SIZE || $value > self::MAX_SIZE) {
            throw new Exception(\sprintf(
                'Размер гроба %d см находится вне допустимого диапазона [%d, %d] см.',
                $value,
                self::MIN_SIZE,
                self::MAX_SIZE
            ));
        }
    }
}
