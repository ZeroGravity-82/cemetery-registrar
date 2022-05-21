<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialChainId;
use Cemetery\Tests\Registrar\Domain\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialChainIdTest extends EntityIdTest
{
    protected string $className = BurialChainId::class;
}
