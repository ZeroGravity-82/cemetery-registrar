<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetails
{
    /**
     * @var Name
     */
    private readonly Name $bankName;

    /**
     * @var Bik
     */
    private readonly Bik $bik;

    /**
     * @var CorrespondentAccount|null
     */
    private readonly ?CorrespondentAccount $correspondentAccount;

    /**
     * @var CurrentAccount
     */
    private readonly CurrentAccount $currentAccount;

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
        $this->bankName = new Name($bankName);
        $this->bik      = new Bik($bik);
        if ($correspondentAccount !== null) {
            $this->assertValidCorrespondentAccount($correspondentAccount);
            $this->correspondentAccount = new CorrespondentAccount($correspondentAccount);
        } else {
            $this->correspondentAccount = null;
        }
        $this->assertValidCurrentAccount($currentAccount);
        $this->currentAccount = new CurrentAccount($currentAccount);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $bankDetailsString = $this->bankName();
        $bankDetailsString .= ', р/счёт ' . $this->currentAccount();
        if ($this->correspondentAccount()) {
            $bankDetailsString .= ', к/счёт ' . $this->correspondentAccount();
        }

        return $bankDetailsString . ', БИК ' . $this->bik();
    }

    /**
     * @return Name
     */
    public function bankName(): Name
    {
        return $this->bankName;
    }

    /**
     * @return Bik
     */
    public function bik(): Bik
    {
        return $this->bik;
    }

    /**
     * @return CorrespondentAccount|null
     */
    public function correspondentAccount(): ?CorrespondentAccount
    {
        return $this->correspondentAccount;
    }

    /**
     * @return CurrentAccount
     */
    public function currentAccount(): CurrentAccount
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
        $isSameBankName             = $bankDetails->bankName()->isEqual($this->bankName());
        $isSameBik                  = $bankDetails->bik()->isEqual($this->bik());
        $isSameCorrespondentAccount = $bankDetails->correspondentAccount() !== null && $this->correspondentAccount() !== null
            ? $bankDetails->correspondentAccount()->isEqual($this->correspondentAccount())
            : $bankDetails->correspondentAccount() === null && $this->correspondentAccount() === null;
        $isSameCurrentAccount       = $bankDetails->currentAccount()->isEqual($this->currentAccount());

        return $isSameBankName && $isSameBik && $isSameCorrespondentAccount && $isSameCurrentAccount;
    }

    /**
     * @param string $correspondentAccount
     */
    private function assertValidCorrespondentAccount(string $correspondentAccount): void
    {
        $this->assertBikNotBelongsToCentralBankOfRussia($this->bik());
        $this->assertNotEmpty($correspondentAccount, CorrespondentAccount::ACCOUNT_TYPE);
        $this->assertMatchesTheBik($correspondentAccount, CorrespondentAccount::ACCOUNT_TYPE, $this->bik());
    }

    /**
     * @param string $currentAccount
     */
    private function assertValidCurrentAccount(string $currentAccount): void
    {
        $this->assertNotEmpty($currentAccount, CurrentAccount::ACCOUNT_TYPE);
        $this->assertMatchesTheBik($currentAccount, CurrentAccount::ACCOUNT_TYPE, $this->bik());
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
        return \substr($bik->value(), -3) . $value;
    }

    /**
     * @param string $value
     * @param Bik    $bik
     *
     * @return string
     */
    private function getStringForCorrespondentAccountCheckSum(string $value, Bik $bik): string
    {
        return '0' . \substr($bik->value(), -5, 2) . $value;
    }
}
