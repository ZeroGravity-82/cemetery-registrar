<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Bik
{
    private const BIK_LENGTH = 9;

    /**
     * @throws Exception when the BIK is empty
     * @throws Exception when the BIK has non-numeric value
     * @throws Exception when the length of the BIK is wrong
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $bik): bool
    {
        return $bik->value() === $this->value();
    }

    public function isBelongToCentralBankOfRussia(): bool
    {
        return \substr($this->value(), -3, 2) === '00';
    }

    /**
     * @throws Exception when the BIK is empty
     * @throws Exception when the BIK has non-numeric value
     * @throws Exception when the length of the BIK is wrong
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
    }

    /**
     * @throws Exception when the BIK is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('БИК не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the BIK has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new Exception('БИК должен состоять только из цифр.');
        }
    }

    /**
     * @throws Exception when the length of the BIK is wrong
     */
    private function assertValidLength(string $value): void
    {
        if (\strlen($value) !== self::BIK_LENGTH) {
            throw new Exception(\sprintf('БИК должен состоять из %d цифр.', self::BIK_LENGTH));
        }
    }
}
