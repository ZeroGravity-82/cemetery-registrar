<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Name
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
     * @param self $name
     *
     * @return bool
     */
    public function isEqual(self $name): bool
    {
        return $name->value() === $this->value();
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
     * @throws \InvalidArgumentException when the name is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('Наименование не может иметь пустое значение.');
        }
    }
}