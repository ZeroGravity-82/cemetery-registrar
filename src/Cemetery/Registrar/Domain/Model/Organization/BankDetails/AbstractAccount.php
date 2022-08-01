<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractAccount
{
    private const ACCOUNT_LENGTH = 20;

    /**
     * @throws Exception when the account is empty
     * @throws Exception when the account has non-numeric value
     * @throws Exception when the length of the account is wrong
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    abstract protected function accountType(): string;

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $account): bool
    {
        return $account->value() === $this->value();
    }

    /**
     * @throws Exception when the account is empty
     * @throws Exception when the account has non-numeric value
     * @throws Exception when the length of the account is wrong
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
    }

    /**
     * @throws Exception when the account is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception(\sprintf('%s не может иметь пустое значение.', $this->accountType()));
        }
    }

    /**
     * @throws Exception when the account has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new Exception(\sprintf('%s должен состоять только из цифр.', $this->accountType()));
        }
    }

    /**
     * @throws Exception when the length of the account is wrong
     */
    private function assertValidLength(string $value): void
    {
        $accountName = $this->accountType();
        if (\strlen($value) !== self::ACCOUNT_LENGTH) {
            throw new Exception(
                \sprintf('%s должен состоять из %d цифр.', $accountName, self::ACCOUNT_LENGTH)
            );
        }
    }
}
