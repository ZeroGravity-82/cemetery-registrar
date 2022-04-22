<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RowInBlock
{
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
     * @param self $accuracy
     *
     * @return bool
     */
    public function isEqual(self $accuracy): bool
    {
        return $accuracy->value() === $this->value();
    }

    /**
     * @param string $accuracy
     */
    private function assertValidValue(string $accuracy): void
    {
        $this->assertNotEmpty($accuracy);
    }

    /**
     * @param string $accuracy
     *
     * @throws \InvalidArgumentException when the row in block value is empty
     */
    private function assertNotEmpty(string $accuracy): void
    {
        if (\trim($accuracy) === '') {
            throw new \InvalidArgumentException('Ряд в квартале не может иметь пустое значение.');
        }
    }
}
