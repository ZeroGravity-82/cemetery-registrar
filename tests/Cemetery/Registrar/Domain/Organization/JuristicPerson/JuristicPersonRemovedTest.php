<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRemoved;
use Cemetery\Tests\Registrar\Domain\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemovedTest extends EventTest
{
    private JuristicPersonId $juristicPersonId;

    public function setUp(): void
    {
        $this->juristicPersonId = new JuristicPersonId('888');
        $this->event            = new JuristicPersonRemoved($this->juristicPersonId);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertSame($this->juristicPersonId, $this->event->juristicPersonId());
    }
}
