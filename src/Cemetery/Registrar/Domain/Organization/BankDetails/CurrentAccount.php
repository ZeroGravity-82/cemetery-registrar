<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CurrentAccount
{
    private const CURR_ACCOUNT_LENGTH = 20;

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
     * @param self $currentAccount
     *
     * @return bool
     */
    public function isEqual(self $currentAccount): bool
    {
        return $currentAccount->getValue() === $this->getValue();
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
     * @throws \InvalidArgumentException when the current account is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('Р/счёт не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the current account has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException('Р/счёт должен состоять только из цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the current account is wrong
     */
    private function assertValidLength(string $value): void
    {
        if (\strlen($value) !== self::CURR_ACCOUNT_LENGTH) {
            throw new \InvalidArgumentException(
                \sprintf('Р/счёт должен состоять из %d цифр.', self::CURR_ACCOUNT_LENGTH)
            );
        }
    }
}
