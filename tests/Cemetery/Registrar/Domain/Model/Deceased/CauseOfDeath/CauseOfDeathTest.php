<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathDescription;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathTest extends AggregateRootTest
{
    private CauseOfDeath $causeOfDeath;
    
    public function setUp(): void
    {
        $id                 = new CauseOfDeathId('CD001');
        $description        = new CauseOfDeathDescription('Некоторая причина смерти');
        $this->causeOfDeath = new CauseOfDeath($id, $description);
        $this->entity       = $this->causeOfDeath;
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(CauseOfDeathId::class, $this->causeOfDeath->id());
        $this->assertSame('CD001', $this->causeOfDeath->id()->value());
        $this->assertInstanceOf(CauseOfDeathDescription::class, $this->causeOfDeath->description());
        $this->assertSame('Некоторая причина смерти', $this->causeOfDeath->description()->value());
    }

    public function testItSetsDescription(): void
    {
        $description = new CauseOfDeathDescription('Другая причина смерти');
        $this->causeOfDeath->setDescription($description);
        $this->assertInstanceOf(CauseOfDeathDescription::class, $this->causeOfDeath->description());
        $this->assertTrue($this->causeOfDeath->description()->isEqual($description));
    }
}
