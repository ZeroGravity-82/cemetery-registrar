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
        $causeOfDeath = new CauseOfDeath('Some reason');
        $this->assertSame('Some reason', $causeOfDeath->getValue());
    }

    public function testItStringifyable(): void
    {
        $causeOfDeath = new CauseOfDeath('Some reason');

        $this->assertSame('Some reason', (string) $causeOfDeath);
    }

    public function testItComparable(): void
    {
        $causeOfDeathA = new CauseOfDeath('Some reason');
        $causeOfDeathB = new CauseOfDeath('Other reason');
        $causeOfDeathC = new CauseOfDeath('Some reason');

        $this->assertFalse($causeOfDeathA->isEqual($causeOfDeathB));
        $this->assertTrue($causeOfDeathA->isEqual($causeOfDeathC));
        $this->assertFalse($causeOfDeathB->isEqual($causeOfDeathC));
    }
}
