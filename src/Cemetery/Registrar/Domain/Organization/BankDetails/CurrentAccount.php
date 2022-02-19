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
     * @param self $currentAccount
     *
     * @return bool
     */
    public function isEqual(self $currentAccount): bool
    {
        $isSameValue = $currentAccount->getValue() === $this->getValue();
        $isSameBik   = $currentAccount->getBik()->isEqual($this->getBik());

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

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @throws \InvalidArgumentException when the current account contains an incorrect check digit
     */
    private function assertValidCheckDigit(string $value, Bik $bik): void
    {
        $checkDigit        = $this->calculateCheckDigit($value, $bik);
        $isCheckDigitValid = $checkDigit === 0;
        if (!$isCheckDigitValid) {
            throw new \InvalidArgumentException('Р/счёт недействителен (не соответствует БИК).');
        }
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return int
     */
    private function calculateCheckDigit(string $value, Bik $bik): int
    {
        $checkSum     = 0;
        $coefficients = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];
        $stringToTest = \substr($bik->getValue(), -3) . $value;
        foreach ($coefficients as $index => $coefficient) {
            $checkSum += $coefficient * ((int) $stringToTest[$index] % 10);
        }

        return $checkSum % 10;
    }
}
