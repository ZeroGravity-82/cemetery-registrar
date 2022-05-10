<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Tests\Registrar\Domain\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialIdTest extends EntityIdTest
{
    protected string $className = BurialId::class;
}
