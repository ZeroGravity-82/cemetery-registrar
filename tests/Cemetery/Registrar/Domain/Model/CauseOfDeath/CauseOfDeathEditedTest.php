<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Tests\Registrar\Domain\Model\AbstractEventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathEditedTest extends AbstractEventTest
{
    private CauseOfDeathId   $id;
    private CauseOfDeathName $name;

    public function setUp(): void
    {
        $this->id    = new CauseOfDeathId('CD001');
        $this->name  = new CauseOfDeathName('Асфиксия');
        $this->event = new CauseOfDeathEdited(
            $this->id,
            $this->name,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->id->isEqual($this->event->id()));
        $this->assertTrue($this->name->isEqual($this->event->name()));
    }
}
