<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CorrespondentAccount extends AbstractAccount
{
    public const ACCOUNT_TYPE = 'К/счёт';

    /**
     * {@inheritdoc}
     */
    protected function getAccountType(): string
    {
        return self::ACCOUNT_TYPE;
    }
}
