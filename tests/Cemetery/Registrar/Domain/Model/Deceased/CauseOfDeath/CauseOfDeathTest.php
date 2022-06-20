<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathName;
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
        $name               = new CauseOfDeathName('Некоторая причина смерти');
        $this->causeOfDeath = new CauseOfDeath($id, $name);
        $this->entity       = $this->causeOfDeath;
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(CauseOfDeathId::class, $this->causeOfDeath->id());
        $this->assertSame('CD001', $this->causeOfDeath->id()->value());
        $this->assertInstanceOf(CauseOfDeathName::class, $this->causeOfDeath->name());
        $this->assertSame('Некоторая причина смерти', $this->causeOfDeath->name()->value());
    }

    public function testItSetsName(): void
    {
        $name = new CauseOfDeathName('Другая причина смерти');
        $this->causeOfDeath->setName($name);
        $this->assertInstanceOf(CauseOfDeathName::class, $this->causeOfDeath->name());
        $this->assertTrue($this->causeOfDeath->name()->isEqual($name));
    }
}
