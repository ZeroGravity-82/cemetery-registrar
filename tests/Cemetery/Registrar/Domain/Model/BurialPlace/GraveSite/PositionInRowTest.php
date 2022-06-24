<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class PositionInRowTest extends TestCase
{
    private const MAX_POSITION = 100;

    public function testItSuccessfullyCreated(): void
    {
        $positionInRow = new PositionInRow(1);
        $this->assertSame(1, $positionInRow->value());

        $positionInRow = new PositionInRow(self::MAX_POSITION);
        $this->assertSame(self::MAX_POSITION, $positionInRow->value());

        $positionInRowAvg = (int) (self::MAX_POSITION / 2);
        $positionInRow    = new PositionInRow($positionInRowAvg);
        $this->assertSame($positionInRowAvg, $positionInRow->value());
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

    public function testItFailsWithTooMuchValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Место в ряду не может иметь значение больше %d.', self::MAX_POSITION));
        new PositionInRow(self::MAX_POSITION + 1);
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
