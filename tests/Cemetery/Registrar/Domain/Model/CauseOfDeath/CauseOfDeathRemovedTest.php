<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemovedTest extends EventTest
{
    private CauseOfDeathId   $causeOfDeathId;
    private CauseOfDeathName $causeOfDeathName;

    public function setUp(): void
    {
        $this->causeOfDeathId   = new CauseOfDeathId('CD001');
        $this->causeOfDeathName = new CauseOfDeathName('Асфиксия');
        $this->event            = new CauseOfDeathRemoved(
            $this->causeOfDeathId,
            $this->causeOfDeathName,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->causeOfDeathId->isEqual($this->event->causeOfDeathId()));
        $this->assertTrue($this->causeOfDeathName->isEqual($this->event->causeOfDeathName()));
    }
}
