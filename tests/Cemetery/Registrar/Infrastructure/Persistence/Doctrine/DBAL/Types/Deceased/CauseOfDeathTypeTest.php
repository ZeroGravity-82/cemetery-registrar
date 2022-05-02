<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Deceased\CauseOfDeathType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathTypeTest extends CustomStringTypeTest
{
    protected string $className = CauseOfDeathType::class;
    protected string $typeName  = 'cause_of_death';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'Некоторая причина смерти';
        $this->phpValue = new CauseOfDeath('Некоторая причина смерти');
    }
}
