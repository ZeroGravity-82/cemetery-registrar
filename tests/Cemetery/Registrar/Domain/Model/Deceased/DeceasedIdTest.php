<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased;

use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Tests\Registrar\Domain\Model\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedIdTest extends EntityIdTest
{
    protected string $className = DeceasedId::class;
}