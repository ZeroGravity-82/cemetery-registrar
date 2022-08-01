<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\BankDetails;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CurrentAccount extends AbstractAccount
{
    public const ACCOUNT_TYPE = 'Р/счёт';

    protected function accountType(): string
    {
        return self::ACCOUNT_TYPE;
    }
}
