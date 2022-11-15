<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialChainId;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialChainIdTest extends AbstractEntityIdTest
{
    protected string $className = BurialChainId::class;
}
