<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\RowInColumbarium;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RowInColumbariumTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $rowInColumbarium = new RowInColumbarium(10);
        $this->assertSame(10, $rowInColumbarium->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ряд в колумбарии не может иметь отрицательное значение.');
        new RowInColumbarium(-2);
    }

    public function testItFailsWithZeroValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ряд в колумбарии не может иметь нулевое значение.');
        new RowInColumbarium(0);
    }

    public function testItStringifyable(): void
    {
        $rowInColumbarium = new RowInColumbarium(10);
        $this->assertSame('10', (string) $rowInColumbarium);
    }

    public function testItComparable(): void
    {
        $rowInColumbariumA = new RowInColumbarium(10);
        $rowInColumbariumB = new RowInColumbarium(11);
        $rowInColumbariumC = new RowInColumbarium(10);
        $this->assertFalse($rowInColumbariumA->isEqual($rowInColumbariumB));
        $this->assertTrue($rowInColumbariumA->isEqual($rowInColumbariumC));
        $this->assertFalse($rowInColumbariumB->isEqual($rowInColumbariumC));
    }
}
