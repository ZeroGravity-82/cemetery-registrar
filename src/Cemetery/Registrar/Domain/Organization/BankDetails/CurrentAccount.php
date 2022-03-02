<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CurrentAccount extends AbstractAccount
{
    private const ACCOUNT_TYPE = 'Р/счёт';

    /**
     * {@inheritdoc}
     */
    protected function assertValidValue(string $value, Bik $bik): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertMatchesTheBik($value, $bik);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAccountType(): string
    {
        return self::ACCOUNT_TYPE;
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
