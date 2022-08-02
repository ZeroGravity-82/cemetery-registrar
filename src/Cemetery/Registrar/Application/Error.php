<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Error
{
    public function __construct(
        private string      $code,
        private string      $message,
        private ?\Exception $cause = null,
    ) {
        $this->assertValidCode($code);
        $this->assertValidMessage($code);
    }

    public function __toString(): string
    {
        return \sprintf('%s (код ошибки: %s)', $this->message(), $this->code());
    }

    public function code(): string
    {
        return $this->code;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function cause(): ?\Exception
    {
        return $this->cause;
    }

    public function isEqual(self $error): bool
    {
        return $error->code() === $this->code();
    }

    private function assertValidCode(string $value): void
    {
        $this->assertNotEmpty('Код ошибки', $value);
    }

    private function assertValidMessage(string $value): void
    {
        $this->assertNotEmpty('Сообщение об ошибке', $value);
    }

    /**
     * @throws \RuntimeException when the value is an empty string
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if (\trim($value) === '') {
            throw new \RuntimeException(\sprintf('%s не может иметь пустое значение.', $name));
        }
    }
}
