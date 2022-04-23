<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\PositionInRow;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class PositionInRowTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $positionInRow = new PositionInRow(10);
        $this->assertSame(10, $positionInRow->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Место в ряду не может иметь отрицательное значение.');
        new PositionInRow(-2);
    }

    public function testItFailsWithZeroValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Место в ряду не может иметь нулевое значение.');
        new PositionInRow(0);
    }

    public function testItStringifyable(): void
    {
        $positionInRow = new PositionInRow(10);
        $this->assertSame('10', (string) $positionInRow);
    }

    public function testItComparable(): void
    {
        $positionInRowA = new PositionInRow(10);
        $positionInRowB = new PositionInRow(11);
        $positionInRowC = new PositionInRow(10);
        $this->assertFalse($positionInRowA->isEqual($positionInRowB));
        $this->assertTrue($positionInRowA->isEqual($positionInRowC));
        $this->assertFalse($positionInRowB->isEqual($positionInRowC));
    }
}
