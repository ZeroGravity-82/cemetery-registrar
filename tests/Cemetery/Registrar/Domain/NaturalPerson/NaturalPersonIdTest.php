<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonIdTest extends EntityIdTest
{
    protected string $className = NaturalPersonId::class;

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('NATURAL_PERSON_ID', NaturalPersonId::CLASS_SHORTCUT);
    }
}
