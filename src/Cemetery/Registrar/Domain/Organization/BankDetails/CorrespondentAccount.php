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
}
