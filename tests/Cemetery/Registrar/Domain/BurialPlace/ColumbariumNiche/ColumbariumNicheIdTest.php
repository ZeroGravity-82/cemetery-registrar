<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Tests\Registrar\Domain\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheIdTest extends EntityIdTest
{
    protected string $className = ColumbariumNicheId::class;

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('COLUMBARIUM_NICHE_ID', ColumbariumNicheId::CLASS_SHORTCUT);
    }
}
