<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractInn
{
    /**
     * @param string $value
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return int
     */
    abstract protected function getInnLength(): int;

    /**
     * @param string $value
     */
    abstract protected function assertValidCheckDigits(string $value): void;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param self $inn
     *
     * @return bool
     */
    public function isEqual(self $inn): bool
    {
        return $inn->getValue() === $this->getValue();
    }

    /**
     * @param string $value
     * @param array  $coefficients
     *
     * @return int
     */
    protected function calculateCheckDigit(string $value, array $coefficients): int
    {
        $checkSum = 0;
        foreach ($coefficients as $index => $coefficient) {
            $checkSum += $coefficient * (int) $value[$index];
        }

        return $checkSum % 11 % 10;
    }

    /**
     * @throws \InvalidArgumentException when the INN contains an incorrect check digits
     */
    protected function throwIncorrectCheckDigitsException(): void
    {
        throw new \InvalidArgumentException('ИНН недействителен.');
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigits($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the INN is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('ИНН не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the INN has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException('ИНН должен состоять только из цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the INN is wrong
     */
    private function assertValidLength(string $value): void
    {
        $innLength = $this->getInnLength();
        if (\strlen($value) !== $innLength) {
            throw new \InvalidArgumentException(\sprintf('ИНН должен состоять из %d цифр.', $innLength));
        }
    }
}
