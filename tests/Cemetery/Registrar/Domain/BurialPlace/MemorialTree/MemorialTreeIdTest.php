<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeIdTest extends AbstractEntityIdTest
{
    protected string $className = MemorialTreeId::class;
}
