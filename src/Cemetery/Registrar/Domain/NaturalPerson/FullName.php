<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FullName
{
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string|null $value
     */
    public function __construct(
        ?string $value,
    ) {
        $this->assertValidValue($value);
        $this->value = $value;
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
     * @param self $fullName
     *
     * @return bool
     */
    public function isEqual(self $fullName): bool
    {
        return $fullName->getValue() === $this->getValue();
    }

    /**
     * @param string|null $value
     */
    private function assertValidValue(?string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @param string|null $value
     *
     * @throws \InvalidArgumentException when the full name is an empty
     */
    private function assertNotEmpty(?string $value): void
    {
        if ($value === '' || $value === null) {
            throw new \InvalidArgumentException('Full name value cannot be empty.');
        }
    }
}
