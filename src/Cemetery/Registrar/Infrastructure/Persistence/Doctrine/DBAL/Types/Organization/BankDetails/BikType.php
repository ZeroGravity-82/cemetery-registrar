<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\Bik;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BikType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Bik::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'bik';
}
