<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Tests\Registrar\Domain\Model\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathIdTest extends EntityIdTest
{
    protected string $className = CauseOfDeathId::class;
}
