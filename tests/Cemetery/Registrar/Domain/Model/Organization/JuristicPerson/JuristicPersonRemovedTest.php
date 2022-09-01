<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemovedTest extends EventTest
{
    private JuristicPersonId $juristicPersonId;

    public function setUp(): void
    {
        $this->juristicPersonId = new JuristicPersonId('888');
        $this->event            = new JuristicPersonRemoved(
            $this->juristicPersonId,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->juristicPersonId->isEqual($this->event->juristicPersonId()));
    }
}
