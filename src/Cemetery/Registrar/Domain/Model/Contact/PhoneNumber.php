<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Contact;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PhoneNumber
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
     * @param self $phoneNumber
     *
     * @return bool
     */
    public function isEqual(self $phoneNumber): bool
    {
        return $phoneNumber->value() === $this->value();
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
     * @throws \InvalidArgumentException when the phone number is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('Номер телефона не может иметь пустое значение.');
        }
    }
}
