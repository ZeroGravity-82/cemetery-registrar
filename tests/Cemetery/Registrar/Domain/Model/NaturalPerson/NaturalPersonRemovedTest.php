<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRemovedTest extends EventTest
{
    private NaturalPersonId $naturalPersonId;

    public function setUp(): void
    {
        $this->naturalPersonId = new NaturalPersonId('NP001');
        $this->event           = new NaturalPersonRemoved(
            $this->naturalPersonId,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->naturalPersonId->isEqual($this->event->naturalPersonId()));
    }
}
