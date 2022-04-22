<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class GraveSiteSize
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
     * @param string $graveSiteSize
     */
    private function assertValidValue(string $graveSiteSize): void
    {
        $this->assertNotEmpty($graveSiteSize);
        $this->assertNotNegative($graveSiteSize);
        $this->assertValidFormat($graveSiteSize);
    }

    /**
     * @param string $graveSiteSize
     *
     * @throws \InvalidArgumentException when the grave site size value is empty
     */
    private function assertNotEmpty(string $graveSiteSize): void
    {
        if (\trim($graveSiteSize) === '') {
            throw new \InvalidArgumentException('Размер участка не может иметь пустое значение.');
        }
    }

    /**
     * @param string $graveSiteSize
     *
     * @throws \InvalidArgumentException when the grave site size value is negative
     */
    private function assertNotNegative(string $graveSiteSize): void
    {
        if (\is_numeric($graveSiteSize) && (float) $graveSiteSize < 0.0) {
            throw new \InvalidArgumentException('Размер участка не может иметь отрицательное значение.');
        }
    }

    /**
     * @param string $graveSiteSize
     *
     * @throws \InvalidArgumentException when the grave site size value has an invalid format
     */
    private function assertValidFormat(string $graveSiteSize): void
    {
        if (!\preg_match(self::VALUE_PATTERN, $graveSiteSize)) {
            throw new \InvalidArgumentException(\sprintf('Размер участка "%s" имеет неверный формат.', $graveSiteSize));
        }
    }
}
