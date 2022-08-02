<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathNameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $causeOfDeathName = new CauseOfDeathName('Некоторая причина смерти');
        $this->assertSame('Некоторая причина смерти', $causeOfDeathName->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new CauseOfDeathName('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new CauseOfDeathName('   ');
    }

    public function testItStringifyable(): void
    {
        $causeOfDeathName = new CauseOfDeathName('Некоторая причина смерти');

        $this->assertSame('Некоторая причина смерти', (string) $causeOfDeathName);
    }

    public function testItComparable(): void
    {
        $causeOfDeathNameA = new CauseOfDeathName('Некоторая причина смерти');
        $causeOfDeathNameB = new CauseOfDeathName('Другая причина смерти');
        $causeOfDeathNameC = new CauseOfDeathName('Некоторая причина смерти');

        $this->assertFalse($causeOfDeathNameA->isEqual($causeOfDeathNameB));
        $this->assertTrue($causeOfDeathNameA->isEqual($causeOfDeathNameC));
        $this->assertFalse($causeOfDeathNameB->isEqual($causeOfDeathNameC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Причина смерти не может иметь пустое значение.');
    }
}
