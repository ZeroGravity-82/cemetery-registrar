<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialCode
{
    private const CODE_MIN_LENGTH = 2;
    private const CODE_MAX_LENGTH = 9;

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
     * @param self $code
     *
     * @return bool
     */
    public function isEqual(self $code): bool
    {
        return $code->value() === $this->value();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
    }

    /**
     * @param string $value
     *
     * @throws \RuntimeException when the code is an empty string
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \RuntimeException('Код захоронения не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \RuntimeException when the code has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \RuntimeException('Код захоронения должен состоять только из цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \RuntimeException when the length of the code is wrong
     */
    private function assertValidLength(string $value): void
    {
        if (\strlen($value) < self::CODE_MIN_LENGTH || \strlen($value) > self::CODE_MAX_LENGTH) {
            throw new \RuntimeException(\sprintf(
                'Код захоронения должен иметь длину от %d до %d цифр.',
                self::CODE_MIN_LENGTH,
                self::CODE_MAX_LENGTH
            ));
        }
    }
}
