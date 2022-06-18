<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathDescription;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathDescriptionTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $causeOfDeathDescription = new CauseOfDeathDescription('Некоторая причина смерти');
        $this->assertSame('Некоторая причина смерти', $causeOfDeathDescription->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new CauseOfDeathDescription('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new CauseOfDeathDescription('   ');
    }

    public function testItStringifyable(): void
    {
        $causeOfDeathDescription = new CauseOfDeathDescription('Некоторая причина смерти');

        $this->assertSame('Некоторая причина смерти', (string) $causeOfDeathDescription);
    }

    public function testItComparable(): void
    {
        $causeOfDeathDescriptionA = new CauseOfDeathDescription('Некоторая причина смерти');
        $causeOfDeathDescriptionB = new CauseOfDeathDescription('Другая причина смерти');
        $causeOfDeathDescriptionC = new CauseOfDeathDescription('Некоторая причина смерти');

        $this->assertFalse($causeOfDeathDescriptionA->isEqual($causeOfDeathDescriptionB));
        $this->assertTrue($causeOfDeathDescriptionA->isEqual($causeOfDeathDescriptionC));
        $this->assertFalse($causeOfDeathDescriptionB->isEqual($causeOfDeathDescriptionC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Причина смерти не может иметь пустое значение.');
    }
}
