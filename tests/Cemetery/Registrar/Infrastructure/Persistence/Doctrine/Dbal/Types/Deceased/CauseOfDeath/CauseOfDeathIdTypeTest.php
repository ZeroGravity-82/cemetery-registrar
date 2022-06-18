<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Deceased\CauseOfDeath\CauseOfDeathIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathIdTypeTest extends CustomStringTypeTest
{
    protected string $className = CauseOfDeathIdType::class;
    protected string $typeName  = 'cause_of_death_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'CD001';
        $this->phpValue = new CauseOfDeathId('CD001');
    }
}
