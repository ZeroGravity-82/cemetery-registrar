<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BankDetails
{
    /**
     * @param string $bankName
     * @param string $rcbic
     * @param string $correspondentAccount
     * @param string $currentAccount
     */
    public function __construct(
        private string $bankName,
        private string $rcbic,
        private string $correspondentAccount,
        private string $currentAccount,
    ) {
        $this->assertValidBankName($bankName);
        $this->assertValidRcbic($rcbic);
        $this->assertValidCorrespondentAccount($correspondentAccount);
        $this->assertValidCurrentAccount($this->currentAccount);
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
            $this->getRcbic(),
        );
    }

    /**
     * @return string
     */
    public function getBankName(): string
    {
        return $this->bankName;
    }

    /**
     * @return string
     */
    public function getRcbic(): string
    {
        return $this->rcbic;
    }

    /**
     * @return string
     */
    public function getCorrespondentAccount(): string
    {
        return $this->correspondentAccount;
    }

    /**
     * @return string
     */
    public function getCurrentAccount(): string
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
        $isSameBankName             = $bankDetails->getBankName() === $this->getBankName();
        $isSameRcbic                = $bankDetails->getRcbic() === $this->getRcbic();
        $isSameCorrespondentAccount = $bankDetails->getCorrespondentAccount() === $this->getCorrespondentAccount();
        $isSameCurrentAccount       = $bankDetails->getCurrentAccount() === $this->getCurrentAccount();

        return $isSameBankName && $isSameRcbic && $isSameCorrespondentAccount && $isSameCurrentAccount;
    }

    /**
     * @param string $bankName
     */
    private function assertValidBankName(string $bankName): void
    {
        $this->assertNotEmpty($bankName, 'наименование банка');
    }

    /**
     * @param string $rcbic
     */
    private function assertValidRcbic(string $rcbic): void
    {
        $this->assertNotEmpty($rcbic, 'БИК');
    }

    /**
     * @param string $correspondentAccount
     */
    private function assertValidCorrespondentAccount(string $correspondentAccount): void
    {
        $this->assertNotEmpty($correspondentAccount, 'корреспондентский счёт');
    }

    /**
     * @param string $currentAccount
     */
    private function assertValidCurrentAccount(string $currentAccount): void
    {
        $this->assertNotEmpty($currentAccount, 'расчётный счёт');
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
