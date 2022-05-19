<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Tests\Registrar\Domain\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonIdTest extends EntityIdTest
{
    protected string $className = JuristicPersonId::class;

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('JURISTIC_PERSON_ID', JuristicPersonId::CLASS_SHORTCUT);
    }
}
