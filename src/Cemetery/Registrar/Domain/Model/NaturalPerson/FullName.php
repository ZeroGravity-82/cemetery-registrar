<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullName
{
    /**
     * @throws Exception when the name is an empty string
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

    public function isEqual(self $fullName): bool
    {
        return $fullName->value() === $this->value();
    }

    /**
     * @throws Exception when the full name is empty
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @throws Exception when the full name is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('ФИО не может иметь пустое значение.');
        }
    }
}
