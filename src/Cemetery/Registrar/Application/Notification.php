<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Notification
{
    /**
     * @var Error[]|array
     */
    private array $errors = [];

    /**
     * @param string          $code
     * @param string          $message
     * @param \Exception|null $exception
     */
    public function addError(string $code, string $message, ?\Exception $exception = null): void
    {
        $this->errors[] = new Error($code, $message, $exception);
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors());
    }

    /**
     * @return Error[]|array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return string|null
     */
    public function errorMessage(): ?string
    {
        return \implode(', ', $this->errors()) ?: null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this->errors() as $error) {
            $result[$error->code()] = $error->message();
        }

        return $result;
    }
}
