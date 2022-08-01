<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractOgrn
{
    /**
     * @throws Exception when the OGRN is empty
     * @throws Exception when the OGRN has non-numeric value
     * @throws Exception when the length of the OGRN is wrong
     * @throws Exception when the OGRN contains an incorrect check digit
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    abstract protected function ogrnName(): string;

    abstract protected function ogrnLength(): int;

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $ogrn): bool
    {
        return $ogrn->value() === $this->value();
    }

    /**
     * @throws Exception when the OGRN is empty
     * @throws Exception when the OGRN has non-numeric value
     * @throws Exception when the length of the OGRN is wrong
     * @throws Exception when the OGRN contains an incorrect check digit
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigit($value);
    }

    /**
     * @throws Exception when the OGRN is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception(\sprintf('%s не может иметь пустое значение.', $this->ogrnName()));
        }
    }

    /**
     * @throws Exception when the OGRN has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new Exception(\sprintf('%s должен состоять только из цифр.', $this->ogrnName()));
        }
    }

    /**
     * @throws Exception when the length of the OGRN is wrong
     */
    private function assertValidLength(string $value): void
    {
        $ogrnName   = $this->ogrnName();
        $ogrnLength = $this->ogrnLength();
        if (\strlen($value) !== $ogrnLength) {
            throw new Exception(\sprintf('%s должен состоять из %d цифр.', $ogrnName, $ogrnLength));
        }
    }

    /**
     * @throws Exception when the OGRN contains an incorrect check digit
     */
    private function assertValidCheckDigit(string $value): void
    {
        $checkDigit = (int) $value[$this->ogrnLength() - 1];
        $checkValue = $this->calculateCheckDigit($value);
        if ($checkDigit !== $checkValue) {
            throw new Exception(\sprintf('%s недействителен.', $this->ogrnName()));
        }
    }

    private function calculateCheckDigit(string $value): int
    {
        $divisorString = (string) ($this->ogrnLength() - 2);

        return (int) \substr(\bcsub(
            \substr($value, 0, -1),
            \bcmul(\bcdiv(\substr($value, 0, -1), $divisorString), $divisorString)
        ), -1);
    }
}
