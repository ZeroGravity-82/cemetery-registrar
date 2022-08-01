<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractOkpo
{
    /**
     * @throws Exception when the OKPO is empty
     * @throws Exception when the OKPO has non-numeric value
     * @throws Exception when the length of the OKPO is wrong
     * @throws Exception when the OKPO contains an incorrect check digit
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    abstract protected function okpoLength(): int;

    abstract protected function coefficientsForFirstCheck(): array;

    abstract protected function coefficientsForSecondCheck(): array;

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $okpo): bool
    {
        return $okpo->value() === $this->value();
    }

    /**
     * @throws Exception when the OKPO is empty
     * @throws Exception when the OKPO has non-numeric value
     * @throws Exception when the length of the OKPO is wrong
     * @throws Exception when the OKPO contains an incorrect check digit
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigit($value);
    }

    /**
     * @throws Exception when the OKPO is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('ОКПО не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the OKPO has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new Exception('ОКПО должен состоять только из цифр.');
        }
    }

    /**
     * @throws Exception when the length of the OKPO is wrong
     */
    private function assertValidLength(string $value): void
    {
        $okpoLength = $this->okpoLength();
        if (\strlen($value) !== $okpoLength) {
            throw new Exception(\sprintf('ОКПО должен состоять из %d цифр.', $okpoLength));
        }
    }

    /**
     * @throws Exception when the OKPO contains an incorrect check digit
     */
    private function assertValidCheckDigit(string $value): void
    {
        $checkDigit = (int) $value[$this->okpoLength() - 1];
        $checkValue = $this->calculateCheckSum($value, $this->coefficientsForFirstCheck()) % 11;
        if ($checkValue === 10) {
            $checkValue = $this->calculateCheckSum($value, $this->coefficientsForSecondCheck()) % 11;
            if ($checkValue === 10) {
                $checkValue = 0;
            }
        }
        if ($checkDigit !== $checkValue) {
            $this->throwInvalidCheckDigitException();
        }
    }

    private function calculateCheckSum(string $value, array $coefficients): int
    {
        $checkSum = 0;
        foreach ($coefficients as $index => $coefficient) {
            $checkSum += $coefficient * (int) $value[$index];
        }

        return $checkSum;
    }

    /**
     * @throws Exception about invalid check digit
     */
    private function throwInvalidCheckDigitException(): void
    {
        throw new Exception('ОКПО недействителен.');
    }
}
