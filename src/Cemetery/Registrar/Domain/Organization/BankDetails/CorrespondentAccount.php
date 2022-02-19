<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CorrespondentAccount extends AbstractAccount
{
    private const ACCOUNT_NAME = 'К/счёт';

    /**
     * {@inheritdoc}
     */
    protected function assertValidValue(string $value, Bik $bik): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigit($value, $bik);
        $this->assertBikNotBelongsToCentralBankOfRussia($bik);
    }

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
        return $this->getStringForCorrespondentAccountCheckSum($value, $bik);
    }

    /**
     * @param Bik $bik
     *
     * @throws \InvalidArgumentException when the BIK belongs to Central Bank of Russia
     */
    private function assertBikNotBelongsToCentralBankOfRussia(Bik $bik): void
    {
        if ($bik->isBelongToCentralBankOfRussia()) {
            throw new \InvalidArgumentException('К/счёт не должен быть указан для данного БИК.');
        }
    }
}
