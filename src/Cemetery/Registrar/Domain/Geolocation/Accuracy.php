<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Geolocation;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Accuracy
{
    private const VALUE_PATTERN = '~^\d+\.\d+$~';            // examples: 0.25, 12.5, etc.

    /**
     * @param string $value
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param self $accuracy
     *
     * @return bool
     */
    public function isEqual(self $accuracy): bool
    {
        return $accuracy->getValue() === $this->getValue();
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
        if ($accuracy === '') {
            throw new \InvalidArgumentException('Accuracy value cannot be empty.');
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
            throw new \InvalidArgumentException('Accuracy value cannot be negative.');
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
            throw new \InvalidArgumentException(\sprintf('Accuracy value "%s" has an invalid format.', $accuracy));
        }
    }
}