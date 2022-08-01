<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractInn
{
    /**
     * @throws Exception when the INN is empty
     * @throws Exception when the INN has non-numeric value
     * @throws Exception when the length of the INN is wrong
     * @throws Exception when the check digits are invalid
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    abstract protected function innLength(): int;

    /**
     * @throws Exception when the check digits are invalid
     */
    abstract protected function assertValidCheckDigits(string $value): void;

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $inn): bool
    {
        return $inn->value() === $this->value();
    }

    protected function calculateCheckDigit(string $value, array $coefficients): int
    {
        $checkSum = 0;
        foreach ($coefficients as $index => $coefficient) {
            $checkSum += $coefficient * (int) $value[$index];
        }

        return $checkSum % 11 % 10;
    }

    /**
     * @throws Exception about invalid check digits
     */
    protected function throwInvalidCheckDigitsException(): void
    {
        throw new Exception('ИНН недействителен.');
    }

    /**
     * @throws Exception when the INN is empty
     * @throws Exception when the INN has non-numeric value
     * @throws Exception when the length of the INN is wrong
     * @throws Exception when the check digits are invalid
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigits($value);
    }

    /**
     * @throws Exception when the INN is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('ИНН не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the INN has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new Exception('ИНН должен состоять только из цифр.');
        }
    }

    /**
     * @throws Exception when the length of the INN is wrong
     */
    private function assertValidLength(string $value): void
    {
        $innLength = $this->innLength();
        if (\strlen($value) !== $innLength) {
            throw new Exception(\sprintf('ИНН должен состоять из %d цифр.', $innLength));
        }
    }
}
