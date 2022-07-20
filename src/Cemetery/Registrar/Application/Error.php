<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Error
{
    /**
     * @param string      $code
     * @param string      $message
     * @param ?\Exception $cause
     */
    public function __construct(
        private readonly string      $code,
        private readonly string      $message,
        private readonly ?\Exception $cause = null,
    ) {
        $this->assertValidCode($code);
        $this->assertValidMessage($code);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf('%s (код ошибки: %s)', $this->message(), $this->code());
    }

    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return \Exception|null
     */
    public function cause(): ?\Exception
    {
        return $this->cause;
    }

    /**
     * @param self $error
     *
     * @return bool
     */
    public function isEqual(self $error): bool
    {
        return $error->code() === $this->code();
    }

    /**
     * @param string $value
     */
    private function assertValidCode(string $value): void
    {
        $this->assertNotEmpty('Код ошибки', $value);
    }

    /**
     * @param string $value
     */
    private function assertValidMessage(string $value): void
    {
        $this->assertNotEmpty('Сообщение об ошибке', $value);
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws \RuntimeException when the value is an empty string
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if (\trim($value) === '') {
            throw new \RuntimeException(\sprintf('%s не может иметь пустое значение.', $name));
        }
    }
}
