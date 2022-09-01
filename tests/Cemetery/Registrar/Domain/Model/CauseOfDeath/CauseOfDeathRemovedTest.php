<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemovedTest extends EventTest
{
    private CauseOfDeathId   $causeOfDeathId;

    public function setUp(): void
    {
        $this->causeOfDeathId = new CauseOfDeathId('CD001');
        $this->event          = new CauseOfDeathRemoved(
            $this->causeOfDeathId,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->causeOfDeathId->isEqual($this->event->causeOfDeathId()));
    }
}
