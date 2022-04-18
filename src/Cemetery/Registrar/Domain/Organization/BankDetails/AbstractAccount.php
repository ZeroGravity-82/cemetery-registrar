<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractAccount
{
    private const ACCOUNT_LENGTH = 20;

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
    abstract protected function getAccountType(): string;

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
     * @param self $account
     *
     * @return bool
     */
    public function isEqual(self $account): bool
    {
        return $account->getValue() === $this->getValue();
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
     * @throws \InvalidArgumentException when the account is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException(\sprintf('%s не может иметь пустое значение.', $this->getAccountType()));
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the account has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException(\sprintf('%s должен состоять только из цифр.', $this->getAccountType()));
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the account is wrong
     */
    private function assertValidLength(string $value): void
    {
        $accountName = $this->getAccountType();
        if (\strlen($value) !== self::ACCOUNT_LENGTH) {
            throw new \InvalidArgumentException(
                \sprintf('%s должен состоять из %d цифр.', $accountName, self::ACCOUNT_LENGTH)
            );
        }
    }
}
