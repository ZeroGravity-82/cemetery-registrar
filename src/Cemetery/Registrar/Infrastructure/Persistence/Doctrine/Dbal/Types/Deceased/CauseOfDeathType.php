<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CauseOfDeathType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CauseOfDeath::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'cause_of_death';
}
