<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractOgrn
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
     * @return string
     */
    abstract protected function getOgrnName(): string;

    /**
     * @return int
     */
    abstract protected function getOgrnLength(): int;


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
     * @param self $ogrn
     *
     * @return bool
     */
    public function isEqual(self $ogrn): bool
    {
        return $ogrn->getValue() === $this->getValue();
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
     * @throws \InvalidArgumentException when the OGRN is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException(\sprintf('%s не может иметь пустое значение.', $this->getOgrnName()));
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OGRN has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException(\sprintf('%s должен состоять только из цифр.', $this->getOgrnName()));
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the OGRN is wrong
     */
    private function assertValidLength(string $value): void
    {
        $ogrnName   = $this->getOgrnName();
        $ogrnLength = $this->getOgrnLength();
        if (\strlen($value) !== $ogrnLength) {
            throw new \InvalidArgumentException(\sprintf('%s должен состоять из %d цифр.', $ogrnName, $ogrnLength));
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OGRN contains an incorrect check digit
     */
    private function assertValidCheckDigit(string $value): void
    {
        $checkDigit = (int) $value[$this->getOgrnLength() - 1];
        $checkValue = $this->calculateCheckDigit($value);
        if ($checkDigit !== $checkValue) {
            throw new \InvalidArgumentException(\sprintf('%s недействителен.', $this->getOgrnName()));
        }
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private function calculateCheckDigit(string $value): int
    {
        $divisorString = (string) ($this->getOgrnLength() - 2);

        return (int) \substr(\bcsub(
            \substr($value, 0, -1),
            \bcmul(\bcdiv(\substr($value, 0, -1), $divisorString), $divisorString)
        ), -1);
    }
}
