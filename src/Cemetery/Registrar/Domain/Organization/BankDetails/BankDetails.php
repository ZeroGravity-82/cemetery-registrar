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
     * @var CorrespondentAccount
     */
    private CorrespondentAccount $correspondentAccount;

    /**
     * @var CurrentAccount
     */
    private CurrentAccount $currentAccount;

    /**
     * @param string $bankName
     * @param string $bik
     * @param string $correspondentAccount
     * @param string $currentAccount
     */
    public function __construct(
        string $bankName,
        string $bik,
        string $correspondentAccount,
        string $currentAccount,
    ) {
        $this->assertValidBankName($bankName);
        $this->assertValidBik($bik);
        $this->assertValidCorrespondentAccount($correspondentAccount);
        $this->assertValidCurrentAccount($currentAccount);
        $this->bankName             = new BankName($bankName);
        $this->bik                  = new Bik($bik);
        $this->correspondentAccount = new CorrespondentAccount($correspondentAccount);
        $this->currentAccount       = new CurrentAccount($currentAccount);
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
     * @return CorrespondentAccount
     */
    public function getCorrespondentAccount(): CorrespondentAccount
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
     * @param string $bankName
     */
    private function assertValidBankName(string $bankName): void
    {
        $this->assertNotEmpty($bankName, 'наименование банка');
    }

    /**
     * @param string $bik
     */
    private function assertValidBik(string $bik): void
    {
        $this->assertNotEmpty($bik, 'БИК');
    }

    /**
     * @param string $correspondentAccount
     */
    private function assertValidCorrespondentAccount(string $correspondentAccount): void
    {
        $this->assertNotEmpty($correspondentAccount, 'к/счёт');
    }

    /**
     * @param string $currentAccount
     */
    private function assertValidCurrentAccount(string $currentAccount): void
    {
        $this->assertNotEmpty($currentAccount, 'р/счёт');
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws \InvalidArgumentException when the value is empty
     */
    private function assertNotEmpty(string $value, string $name): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException(\sprintf('%s не может иметь пустое значение.', \ucfirst($name)));
        }
    }
}
