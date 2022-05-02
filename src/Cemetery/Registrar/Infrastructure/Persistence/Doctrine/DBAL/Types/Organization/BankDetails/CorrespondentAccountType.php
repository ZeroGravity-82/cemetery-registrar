<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\CorrespondentAccount;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CorrespondentAccountType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CorrespondentAccount::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'correspondent_account';
}
