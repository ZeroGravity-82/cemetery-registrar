<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CauseOfDeathType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathTypeTest extends AbstractStringTypeTest
{
    protected string $className = CauseOfDeathType::class;

    protected string $typeName = 'cause_of_death';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'Некоторая причина смерти';
        $this->phpValue = new CauseOfDeath('Некоторая причина смерти');
    }
}
