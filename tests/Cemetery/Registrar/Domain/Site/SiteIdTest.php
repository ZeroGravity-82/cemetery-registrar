<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Site;

use Cemetery\Registrar\Domain\Site\SiteId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SiteIdTest extends AbstractEntityIdTest
{
    protected string $className = SiteId::class;
}
