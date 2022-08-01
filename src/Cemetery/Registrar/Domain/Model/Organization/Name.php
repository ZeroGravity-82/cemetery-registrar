<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Name
{
    /**
     * @throws Exception when the name is empty
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

    public function isEqual(self $name): bool
    {
        return $name->value() === $this->value();
    }

    /**
     * @throws Exception when the name is empty
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @throws Exception when the name is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Наименование не может иметь пустое значение.');
        }
    }
}
