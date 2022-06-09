<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Error
{
    private const VALUE_PATTERN = '~^\d+(?:\.\d+)?$~';            // examples: 0.25, 0, 12.5, 1, etc.

    /**
     * @var string
     */
    private readonly string $value;

    /**
     * @param string $value
     */
    public function __construct(
        string $value,
    ) {
        $this->assertValidValue($value);
        $this->value = $this->format($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value();
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->format($this->value);
    }

    /**
     * @param self $error
     *
     * @return bool
     */
    public function isEqual(self $error): bool
    {
        return $this->format($error->value()) === $this->value();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNotNegative($value);
        $this->assertValidFormat($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the error value is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('Погрешность не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the error value is negative
     */
    private function assertNotNegative(string $value): void
    {
        if (\is_numeric($value) && (float) $value < 0.0) {
            throw new \InvalidArgumentException('Погрешность не может иметь отрицательное значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the error value has an invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match(self::VALUE_PATTERN, $value)) {
            throw new \InvalidArgumentException(\sprintf('Погрешность "%s" имеет неверный формат.', $value));
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function format(string $value): string
    {
        $value = $this->addDecimalPoint($value);
        $value = $this->trimPrecedingZeros($value);

        return $this->trimTrailingZeros($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function addDecimalPoint(string $value): string
    {
        if (!\str_contains($value, '.')) {
            $value .= '.0';
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function trimPrecedingZeros(string $value): string
    {
        $value = \ltrim($value, '0');
        if (\str_starts_with($value, '.')) {
            $value = '0' . $value;
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function trimTrailingZeros(string $value): string
    {
        $value = \rtrim($value, '0');
        if (\str_ends_with($value, '.')) {
            $value = $value . '0';
        }

        return $value;
    }
}
