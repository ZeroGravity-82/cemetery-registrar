<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Tests\Registrar\Domain\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteIdTest extends EntityIdTest
{
    protected string $className = GraveSiteId::class;

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('GRAVE_SITE_ID', GraveSiteId::CLASS_SHORTCUT);
    }
}
