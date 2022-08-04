<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Notification
{
    private array $errors = [];

    public function __toString(): string
    {
        return \implode(', ', $this->errors()) ?: '';
    }

    public function addError(string $code, string $message, ?\Exception $exception = null): void
    {
        $this->errors[] = new Error($code, $message, $exception);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors());
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->errors() as $error) {
            $result[$error->code()] = $error->message();
        }

        return $result;
    }
}
