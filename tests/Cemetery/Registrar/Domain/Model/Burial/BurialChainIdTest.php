<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialChainId;
use Cemetery\Tests\Registrar\Domain\Model\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialChainIdTest extends EntityIdTest
{
    protected string $className = BurialChainId::class;
}
