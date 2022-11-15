<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemovedTest extends AbstractEventTest
{
    private CauseOfDeathId $id;

    public function setUp(): void
    {
        $this->id    = new CauseOfDeathId('CD001');
        $this->event = new CauseOfDeathRemoved(
            $this->id,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
    }
}
