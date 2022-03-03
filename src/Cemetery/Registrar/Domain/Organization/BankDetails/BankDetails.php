<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BankDetails
{
    /**
     * @var BankName
     */
    private BankName $bankName;

    /**
     * @var Bik
     */
    private Bik $bik;

    /**
     * @var CorrespondentAccount|null
     */
    private ?CorrespondentAccount $correspondentAccount = null;

    /**
     * @var CurrentAccount
     */
    private CurrentAccount $currentAccount;

    /**
     * @param string      $bankName
     * @param string      $bik
     * @param string|null $correspondentAccount
     * @param string      $currentAccount
     */
    public function __construct(
        string  $bankName,
        string  $bik,
        ?string $correspondentAccount,
        string  $currentAccount,
    ) {
        $this->bankName = new BankName($bankName);
        $this->bik      = new Bik($bik);
        if ($correspondentAccount !== null) {
            $this->assertValidCorrespondentAccount($correspondentAccount);
            $this->correspondentAccount = new CorrespondentAccount($correspondentAccount);
        }
        $this->assertValidCurrentAccount($currentAccount);
        $this->currentAccount = new CurrentAccount($currentAccount);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            '%s, р/счёт %s, к/счёт %s, БИК %s',
            $this->getBankName(),
            $this->getCurrentAccount(),
            $this->getCorrespondentAccount(),
            $this->getBik(),
        );
    }

    /**
     * @return BankName
     */
    public function getBankName(): BankName
    {
        return $this->bankName;
    }

    /**
     * @return Bik
     */
    public function getBik(): Bik
    {
        return $this->bik;
    }

    /**
     * @return CorrespondentAccount|null
     */
    public function getCorrespondentAccount(): ?CorrespondentAccount
    {
        return $this->correspondentAccount;
    }

    /**
     * @return CurrentAccount
     */
    public function getCurrentAccount(): CurrentAccount
    {
        return $this->currentAccount;
    }

    /**
     * @param self $bankDetails
     *
     * @return bool
     */
    public function isEqual(self $bankDetails): bool
    {
        $isSameBankName             = $bankDetails->getBankName()->isEqual($this->getBankName());
        $isSameBik                  = $bankDetails->getBik()->isEqual($this->getBik());
        $isSameCorrespondentAccount = $bankDetails->getCorrespondentAccount()->isEqual($this->getCorrespondentAccount());
        $isSameCurrentAccount       = $bankDetails->getCurrentAccount()->isEqual($this->getCurrentAccount());

        return $isSameBankName && $isSameBik && $isSameCorrespondentAccount && $isSameCurrentAccount;
    }

    /**
     * @param string $correspondentAccount
     */
    private function assertValidCorrespondentAccount(string $correspondentAccount): void
    {
        $this->assertBikNotBelongsToCentralBankOfRussia($this->getBik());
        $this->assertNotEmpty($correspondentAccount, CorrespondentAccount::ACCOUNT_TYPE);
        $this->assertMatchesTheBik($correspondentAccount, CorrespondentAccount::ACCOUNT_TYPE, $this->getBik());
    }

    /**
     * @param string $currentAccount
     */
    private function assertValidCurrentAccount(string $currentAccount): void
    {
        $this->assertNotEmpty($currentAccount, CurrentAccount::ACCOUNT_TYPE);
        $this->assertMatchesTheBik($currentAccount, CurrentAccount::ACCOUNT_TYPE, $this->getBik());
    }

    /**
     * @param Bik $bik
     *
     * @throws \InvalidArgumentException when the BIK belongs to Central Bank of Russia
     */
    private function assertBikNotBelongsToCentralBankOfRussia(Bik $bik): void
    {
        if ($bik->isBelongToCentralBankOfRussia()) {
            throw new \InvalidArgumentException('К/счёт не может быть указан для данного БИК.');
        }
    }

    /**
     * @param string $value
     * @param string $type
     *
     * @throws \InvalidArgumentException when the value is empty
     */
    private function assertNotEmpty(string $value, string $type): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException(\sprintf('%s не может иметь пустое значение.', $type));
        }
    }

    /**
     * @param string $value
     * @param string $accountType
     * @param Bik    $bik
     *
     * @throws \InvalidArgumentException when the account doesn't match the BIK
     */
    private function assertMatchesTheBik(string $value, string $accountType, Bik $bik): void
    {
        $checkValue = $this->calculateCheckValue($value, $accountType, $bik);
        if ($checkValue !== 0) {
            throw new \InvalidArgumentException(
                \sprintf('%s недействителен (не соответствует БИК).', $accountType)
            );
        }
    }

    /**
     * @param string $value
     * @param string $accountType
     * @param Bik    $bik
     *
     * @return int
     */
    private function calculateCheckValue(string $value, string $accountType, Bik $bik): int
    {
        $checkSum          = 0;
        $coefficients      = [7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];
        $stringForCheckSum = $this->getStringForCheckSum($value, $accountType, $bik);
        foreach ($coefficients as $index => $coefficient) {
            $checkSum += $coefficient * ((int) $stringForCheckSum[$index] % 10);
        }

        return $checkSum % 10;
    }

    /**
     * @param string $value
     * @param string $accountType
     * @param Bik    $bik
     *
     * @return string
     */
    private function getStringForCheckSum(string $value, string $accountType, Bik $bik): string
    {
        return match ($accountType) {
            CorrespondentAccount::ACCOUNT_TYPE => $this->getStringForCorrespondentAccountCheckSum($value, $bik),
            CurrentAccount::ACCOUNT_TYPE       =>  match ($bik->isBelongToCentralBankOfRussia()) {
                false => $this->getStringForCurrentAccountCheckSum($value, $bik),
                true  => $this->getStringForCorrespondentAccountCheckSum($value, $bik),
            }
        };
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return string
     */
    private function getStringForCurrentAccountCheckSum(string $value, Bik $bik): string
    {
        return \substr($bik->getValue(), -3) . $value;
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return string
     */
    private function getStringForCorrespondentAccountCheckSum(string $value, Bik $bik): string
    {
        return '0' . \substr($bik->getValue(), -5, 2) . $value;
    }
}
