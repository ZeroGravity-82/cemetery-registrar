<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSize
{
    private const FORMAT = '~^\d+(?:\.\d+)?$~';            // examples: 0.25, 2, 12.5, etc.

    /**
     * @throws Exception when the grave site size value is empty
     * @throws Exception when the grave site size value is negative
     * @throws Exception when the grave site size value is zero
     * @throws Exception when the grave site size value has an invalid format
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $graveSiteSize): bool
    {
        return $graveSiteSize->value() === $this->value();
    }

    public static function isValidFormat(string $value): bool
    {
        return \preg_match(self::FORMAT, $value) === 1;
    }

    /**
     * @throws Exception when the grave site size value is empty
     * @throws Exception when the grave site size value is negative
     * @throws Exception when the grave site size value has an invalid format
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNotNegative($value);
        $this->assertNotZero($value);
        $this->assertValidFormat($value);
    }

    /**
     * @throws Exception when the grave site size value is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Размер участка не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the grave site size value is negative
     */
    private function assertNotNegative(string $value): void
    {
        if (\is_numeric($value) && (float) $value < 0.0) {
            throw new Exception('Размер участка не может иметь отрицательное значение.');
        }
    }

    /**
     * @throws Exception when the grave site size value is zero
     */
    private function assertNotZero(string $value): void
    {
        if (\is_numeric($value) && (int) $value === 0) {
            throw new Exception('Размер участка не может иметь нулевое значение.');
        }
    }

    /**
     * @throws Exception when the grave site size value has an invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match(self::FORMAT, $value)) {
            throw new Exception('Неверный формат размера участка.');
        }
    }
}
