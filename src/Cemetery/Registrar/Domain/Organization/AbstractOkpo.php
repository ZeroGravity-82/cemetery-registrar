<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractOkpo
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
     * @return int
     */
    abstract protected function okpoLength(): int;

    /**
     * @return array
     */
    abstract protected function coefficientsForTheFirstCheck(): array;

    /**
     * @return array
     */
    abstract protected function coefficientsForTheSecondCheck(): array;

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
     * @param self $okpo
     *
     * @return bool
     */
    public function isEqual(self $okpo): bool
    {
        return $okpo->value() === $this->value();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigit($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OKPO is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('ОКПО не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OKPO has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException('ОКПО должен состоять только из цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the OKPO is wrong
     */
    private function assertValidLength(string $value): void
    {
        $okpoLength = $this->okpoLength();
        if (\strlen($value) !== $okpoLength) {
            throw new \InvalidArgumentException(\sprintf('ОКПО должен состоять из %d цифр.', $okpoLength));
        }
    }

    /**
     * @param string $value
     */
    private function assertValidCheckDigit(string $value): void
    {
        $checkDigit = (int) $value[$this->okpoLength() - 1];
        $checkValue = $this->calculateCheckSum($value, $this->coefficientsForTheFirstCheck()) % 11;
        if ($checkValue === 10) {
            $checkValue = $this->calculateCheckSum($value, $this->coefficientsForTheSecondCheck()) % 11;
            if ($checkValue === 10) {
                $checkValue = 0;
            }
        }
        if ($checkDigit !== $checkValue) {
            $this->throwIncorrectCheckDigitException();
        }
    }

    /**
     * @param string $value
     * @param array  $coefficients
     *
     * @return int
     */
    private function calculateCheckSum(string $value, array $coefficients): int
    {
        $checkSum = 0;
        foreach ($coefficients as $index => $coefficient) {
            $checkSum += $coefficient * (int) $value[$index];
        }

        return $checkSum;
    }

    /**
     * @throws \InvalidArgumentException when the OKPO contains an incorrect check digit
     */
    private function throwIncorrectCheckDigitException(): void
    {
        throw new \InvalidArgumentException('ОКПО недействителен.');
    }
}
