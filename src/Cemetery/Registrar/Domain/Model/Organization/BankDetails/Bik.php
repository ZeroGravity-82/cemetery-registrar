<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Bik
{
    private const BIK_LENGTH = 9;

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
     * @param self $bik
     *
     * @return bool
     */
    public function isEqual(self $bik): bool
    {
        return $bik->value() === $this->value();
    }

    /**
     * @return bool
     */
    public function isBelongToCentralBankOfRussia(): bool
    {
        return \substr($this->value(), -3, 2) === '00';
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
     * @throws \InvalidArgumentException when the BIK is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('БИК не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the BIK has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException('БИК должен состоять только из цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the BIK is wrong
     */
    private function assertValidLength(string $value): void
    {
        if (\strlen($value) !== self::BIK_LENGTH) {
            throw new \InvalidArgumentException(\sprintf('БИК должен состоять из %d цифр.', self::BIK_LENGTH));
        }
    }
}