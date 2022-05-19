<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Tests\Registrar\Domain\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeIdTest extends EntityIdTest
{
    protected string $className = MemorialTreeId::class;

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('MEMORIAL_TREE_ID', MemorialTreeId::CLASS_SHORTCUT);
    }
}
