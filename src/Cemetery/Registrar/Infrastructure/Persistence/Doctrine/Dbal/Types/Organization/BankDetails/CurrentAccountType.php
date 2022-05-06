<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\CurrentAccount;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CurrentAccountType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CurrentAccount::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'current_account';
}
