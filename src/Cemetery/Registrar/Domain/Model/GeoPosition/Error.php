<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\GeoPosition;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Error
{
    private const FORMAT = '~^\d+(?:\.\d+)?$~';            // examples: 0.25, 0, 12.5, 1, etc.

    private string $value;

    /**
     * @throws Exception when the error value is empty
     * @throws Exception when the error value is negative
     * @throws Exception when the error value has an invalid format
     */
    public function __construct(
        string $value,
    ) {
        $value = \trim($value);
        $this->assertValidValue($value);
        $this->value = $this->format($value);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->format($this->value);
    }

    public function isEqual(self $error): bool
    {
        return $this->format($error->value()) === $this->value();
    }

    public static function isValidFormat(string $value): bool
    {
        return \preg_match(self::FORMAT, \trim($value)) === 1;
    }

    /**
     * @throws Exception when the error value is empty
     * @throws Exception when the error value is negative
     * @throws Exception when the error value has an invalid format
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNotNegative($value);
        $this->assertValidFormat($value);
    }

    /**
     * @throws Exception when the error value is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Погрешность не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the error value is negative
     */
    private function assertNotNegative(string $value): void
    {
        if (\is_numeric($value) && (float) $value < 0.0) {
            throw new Exception('Погрешность не может иметь отрицательное значение.');
        }
    }

    /**
     * @throws Exception when the error value has an invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match(self::FORMAT, $value)) {
            throw new Exception('Неверный формат погрешности.');
        }
    }

    private function format(string $value): string
    {
        $value = $this->addDecimalPoint($value);
        $value = $this->trimPrecedingZeros($value);

        return $this->trimTrailingZeros($value);
    }

    private function addDecimalPoint(string $value): string
    {
        if (!\str_contains($value, '.')) {
            $value .= '.0';
        }

        return $value;
    }

    private function trimPrecedingZeros(string $value): string
    {
        $value = \ltrim($value, '0');
        if (\str_starts_with($value, '.')) {
            $value = '0' . $value;
        }

        return $value;
    }

    private function trimTrailingZeros(string $value): string
    {
        $value = \rtrim($value, '0');
        if (\str_ends_with($value, '.')) {
            $value = $value . '0';
        }

        return $value;
    }
}
