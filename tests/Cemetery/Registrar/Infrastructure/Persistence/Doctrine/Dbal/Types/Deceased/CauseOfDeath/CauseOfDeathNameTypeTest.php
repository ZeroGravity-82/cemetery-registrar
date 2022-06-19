<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\CauseOfDeath\CauseOfDeathNameType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathNameTypeTest extends CustomStringTypeTest
{
    protected string $className = CauseOfDeathNameType::class;
    protected string $typeName  = 'cause_of_death_name';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'Некоторая причина смерти';
        $this->phpValue = new CauseOfDeathName('Некоторая причина смерти');
    }
}
