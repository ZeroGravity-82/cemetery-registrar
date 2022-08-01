<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Contact;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Email
{
    /**
     * @throws Exception when the e-mail address is empty
     * @throws Exception when the e-mail address has invalid format
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

    public function isEqual(self $email): bool
    {
        return $email->value() === $this->value();
    }

    /**
     * @throws Exception when the e-mail address is empty
     * @throws Exception when the e-mail address has invalid format
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidFormat($value);
    }

    /**
     * @throws Exception when the e-mail address is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Адрес электронной почты не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the e-mail address has invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (\filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exception('Неверный формат адреса электронной почты.');
        }
    }
}
