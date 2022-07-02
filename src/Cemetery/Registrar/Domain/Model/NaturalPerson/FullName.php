<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\Exception\FullNameException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullName
{
    /**
     * @param string $value
     *
     * @throws FullNameException when the full name is empty
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
     *
     * @throws FullNameException when the full name is empty
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @param string $value
     *
     * @throws FullNameException when the full name is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw FullNameException::emptyFullName();
        }
    }
}
