<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Accuracy
{
    private const VALUE_PATTERN = '~^\d+\.\d+$~';            // examples: 0.25, 12.5, etc.

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
     * @param self $accuracy
     *
     * @return bool
     */
    public function isEqual(self $accuracy): bool
    {
        return $this->format($accuracy->value()) === $this->value();
    }

    /**
     * @param string $accuracy
     */
    private function assertValidValue(string $accuracy): void
    {
        $this->assertNotEmpty($accuracy);
        $this->assertNotNegative($accuracy);
        $this->assertValidFormat($accuracy);
    }

    /**
     * @param string $accuracy
     *
     * @throws \InvalidArgumentException when the accuracy value is empty
     */
    private function assertNotEmpty(string $accuracy): void
    {
        if (\trim($accuracy) === '') {
            throw new \InvalidArgumentException('Погрешность не может иметь пустое значение.');
        }
    }

    /**
     * @param string $accuracy
     *
     * @throws \InvalidArgumentException when the accuracy value is negative
     */
    private function assertNotNegative(string $accuracy): void
    {
        if (\is_numeric($accuracy) && (float) $accuracy < 0.0) {
            throw new \InvalidArgumentException('Погрешность не может иметь отрицательное значение.');
        }
    }

    /**
     * @param string $accuracy
     *
     * @throws \InvalidArgumentException when the accuracy value has an invalid format
     */
    private function assertValidFormat(string $accuracy): void
    {
        if (!\preg_match(self::VALUE_PATTERN, $accuracy)) {
            throw new \InvalidArgumentException(\sprintf('Погрешность "%s" имеет неверный формат.', $accuracy));
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function format(string $value): string
    {
        $value = $this->trimPrecedingZeros($value);
        return $this->trimTrailingZeros($value);
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
