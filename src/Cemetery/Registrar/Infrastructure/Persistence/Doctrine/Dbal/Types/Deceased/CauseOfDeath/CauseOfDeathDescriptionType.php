<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathDescription;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathDescriptionType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = CauseOfDeathDescription::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'cause_of_death_description';
}
