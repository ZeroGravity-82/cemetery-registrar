<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CurrentAccount extends AbstractAccount
{
    private const ACCOUNT_NAME = 'Р/счёт';

    /**
     * {@inheritdoc}
     */
    protected function getAccountName(): string
    {
        return self::ACCOUNT_NAME;
    }

    /**
     * {@inheritdoc}
     */
    protected function getStringForCheckSum(string $value, Bik $bik): string
    {
        return match ($bik->isBelongToCentralBankOfRussia()) {
            false => $this->getStringForCurrentAccountCheckSum($value, $bik),
            true  => $this->getStringForCorrespondentAccountCheckSum($value, $bik),
        };
    }
}
