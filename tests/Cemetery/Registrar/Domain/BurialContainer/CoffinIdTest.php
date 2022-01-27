<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\CoffinId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinIdTest extends AbstractEntityIdTest
{
    protected string $className = CoffinId::class;
}
