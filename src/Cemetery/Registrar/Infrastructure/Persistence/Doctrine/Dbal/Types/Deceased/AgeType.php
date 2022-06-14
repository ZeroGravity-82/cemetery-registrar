<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased;

use Cemetery\Registrar\Domain\Model\Deceased\Age;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomIntegerType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AgeType extends CustomIntegerType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Age::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'age';
}
