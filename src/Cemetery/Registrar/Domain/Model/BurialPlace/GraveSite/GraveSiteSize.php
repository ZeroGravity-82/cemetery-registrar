<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSize
{
    private const VALUE_PATTERN = '~^\d+\.\d+$~';            // examples: 0.25, 12.5, etc.

    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
    ) {
        $this->assertValidValue($value);
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
        return $this->value;
    }

    /**
     * @param self $graveSiteSize
     *
     * @return bool
     */
    public function isEqual(self $graveSiteSize): bool
    {
        return $graveSiteSize->value() === $this->value();
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
     * @throws \InvalidArgumentException when the grave site size value is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('Размер участка не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the grave site size value is negative
     */
    private function assertNotNegative(string $value): void
    {
        if (\is_numeric($value) && (float) $value < 0.0) {
            throw new \InvalidArgumentException('Размер участка не может иметь отрицательное значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the grave site size value has an invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match(self::VALUE_PATTERN, $value)) {
            throw new \InvalidArgumentException(\sprintf('Размер участка "%s" имеет неверный формат.', $value));
        }
    }
}
