<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CorrespondentAccount extends AbstractAccount
{
    public const ACCOUNT_TYPE = 'К/счёт';

    protected function accountType(): string
    {
        return self::ACCOUNT_TYPE;
    }
}
