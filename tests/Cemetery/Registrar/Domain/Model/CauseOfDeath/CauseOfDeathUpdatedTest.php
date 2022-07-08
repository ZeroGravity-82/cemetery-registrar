<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathUpdated;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathUpdatedTest extends EventTest
{
    private CauseOfDeathId   $causeOfDeathId;
    private CauseOfDeathName $causeOfDeathName;

    public function setUp(): void
    {
        $this->causeOfDeathId   = new CauseOfDeathId('CD001');
        $this->causeOfDeathName = new CauseOfDeathName('Асфиксия');
        $this->event            = new CauseOfDeathUpdated(
            $this->causeOfDeathId,
            $this->causeOfDeathName,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertSame($this->causeOfDeathId, $this->event->causeOfDeathId());
        $this->assertSame($this->causeOfDeathName, $this->event->causeOfDeathName());
    }
}
