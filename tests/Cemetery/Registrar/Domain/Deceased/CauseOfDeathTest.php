<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\CauseOfDeath;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $causeOfDeath = new CauseOfDeath('Некоторая причина смерти');
        $this->assertSame('Некоторая причина смерти', $causeOfDeath->getValue());
    }

    public function testItStringifyable(): void
    {
        $causeOfDeath = new CauseOfDeath('Некоторая причина смерти');

        $this->assertSame('Некоторая причина смерти', (string) $causeOfDeath);
    }

    public function testItComparable(): void
    {
        $causeOfDeathA = new CauseOfDeath('Некоторая причина смерти');
        $causeOfDeathB = new CauseOfDeath('Другая причина смерти');
        $causeOfDeathC = new CauseOfDeath('Некоторая причина смерти');

        $this->assertFalse($causeOfDeathA->isEqual($causeOfDeathB));
        $this->assertTrue($causeOfDeathA->isEqual($causeOfDeathC));
        $this->assertFalse($causeOfDeathB->isEqual($causeOfDeathC));
    }
}
