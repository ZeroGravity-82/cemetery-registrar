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
     * @param Bik    $bik
     */
    public function __construct(
        private string $value,
        private Bik    $bik,
    ) {
        $this->assertValidValue($value, $bik);
    }

    /**
     * @return string
     */
    abstract protected function getAccountName(): string;

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return string
     */
    abstract protected function getStringForCheckSum(string $value, Bik $bik): string;

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
     * @return Bik
     */
    public function getBik(): Bik
    {
        return $this->bik;
    }

    /**
     * @param self $account
     *
     * @return bool
     */
    public function isEqual(self $account): bool
    {
        $isSameValue = $account->getValue() === $this->getValue();
        $isSameBik   = $account->getBik()->isEqual($this->getBik());

        return $isSameValue && $isSameBik;
    }

    /**
     * @param string $value
     * @param Bik    $bik
     */
    private function assertValidValue(string $value, Bik $bik): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigit($value, $bik);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the account is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException(\sprintf('%s не может иметь пустое значение.', $this->getAccountName()));
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
            throw new \InvalidArgumentException(\sprintf('%s должен состоять только из цифр.', $this->getAccountName()));
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the account is wrong
     */
    private function assertValidLength(string $value): void
    {
        $accountName   = $this->getAccountName();
        $accountLength = self::ACCOUNT_LENGTH;
        if (\strlen($value) !== $accountLength) {
            throw new \InvalidArgumentException(
                \sprintf('%s должен состоять из %d цифр.', $accountName, $accountLength)
            );
        }
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @throws \InvalidArgumentException when the account contains an incorrect check digit
     */
    private function assertValidCheckDigit(string $value, Bik $bik): void
    {
        $checkDigit        = $this->calculateCheckDigit($value, $bik);
        $isCheckDigitValid = $checkDigit === 0;
        if (!$isCheckDigitValid) {
            throw new \InvalidArgumentException(
                \sprintf('%s недействителен (не соответствует БИК).', $this->getAccountName())
            );
        }
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return string
     */
    protected function getStringForCurrentAccountCheckSum(string $value, Bik $bik): string
    {
        return \substr($bik->getValue(), -3) . $value;
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return string
     */
    protected function getStringForCorrespondentAccountCheckSum(string $value, Bik $bik): string
    {
        return '0' . \substr($bik->getValue(), -5, 2) . $value;
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return int
     */
    private function calculateCheckDigit(string $value, Bik $bik): int
    {
        $checkSum          = 0;
        $coefficients      = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];
        $stringForCheckSum = $this->getStringForCheckSum($value, $bik);
        foreach ($coefficients as $index => $coefficient) {
            $checkSum += $coefficient * ((int) $stringForCheckSum[$index] % 10);
        }

        return $checkSum % 10;
    }
}
