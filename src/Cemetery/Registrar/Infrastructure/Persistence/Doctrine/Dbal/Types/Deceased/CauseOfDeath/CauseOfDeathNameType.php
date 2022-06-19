<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathNameType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CauseOfDeathName::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'cause_of_death_name';
}
