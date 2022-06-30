<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CauseOfDeathId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'cause_of_death_id';
}
