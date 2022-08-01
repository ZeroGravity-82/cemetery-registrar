<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Contact;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PhoneNumber
{
    /**
     * @throws Exception when the phone number is empty
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

    public function isEqual(self $phoneNumber): bool
    {
        return $phoneNumber->value() === $this->value();
    }

    /**
     * @throws Exception when the phone number is empty
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @throws Exception when the phone number is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Номер телефона не может иметь пустое значение.');
        }
    }
}
