<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullName
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
     * @param self $fullName
     *
     * @return bool
     */
    public function isEqual(self $fullName): bool
    {
        return $fullName->value() === $this->value();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the full name is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('ФИО не может иметь пустое значение.');
        }
    }
}
