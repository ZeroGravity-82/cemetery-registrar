<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Contact;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Email
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
     * @param self $email
     *
     * @return bool
     */
    public function isEqual(self $email): bool
    {
        return $email->value() === $this->value();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidFormat($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the e-mail address is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('Адрес электронной почты не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the e-mail address has invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (\filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Адрес электронной почты имеет неверный формат.');
        }
    }
}
