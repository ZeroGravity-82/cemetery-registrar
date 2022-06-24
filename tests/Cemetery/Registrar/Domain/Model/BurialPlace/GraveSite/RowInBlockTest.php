<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RowInBlockTest extends TestCase
{
    private const MAX_ROW = 100;

    public function testItSuccessfullyCreated(): void
    {
        $rowInBlock = new RowInBlock(1);
        $this->assertSame(1, $rowInBlock->value());

        $rowInBlock = new RowInBlock(self::MAX_ROW);
        $this->assertSame(self::MAX_ROW, $rowInBlock->value());

        $rowInBlockAvg = (int) (self::MAX_ROW / 2);
        $rowInBlock    = new RowInBlock($rowInBlockAvg);
        $this->assertSame($rowInBlockAvg, $rowInBlock->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ряд в квартале не может иметь отрицательное значение.');
        new RowInBlock(-2);
    }

    public function testItFailsWithZeroValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ряд в квартале не может иметь нулевое значение.');
        new RowInBlock(0);
    }

    public function testItFailsWithTooMuchValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Ряд в квартале не может иметь значение больше %d.', self::MAX_ROW));
        new RowInBlock(self::MAX_ROW + 1);
    }

    public function testItStringifyable(): void
    {
        $rowInBlock = new RowInBlock(10);
        $this->assertSame('10', (string) $rowInBlock);
    }

    public function testItComparable(): void
    {
        $rowInBlockA = new RowInBlock(10);
        $rowInBlockB = new RowInBlock(11);
        $rowInBlockC = new RowInBlock(10);
        $this->assertFalse($rowInBlockA->isEqual($rowInBlockB));
        $this->assertTrue($rowInBlockA->isEqual($rowInBlockC));
        $this->assertFalse($rowInBlockB->isEqual($rowInBlockC));
    }
}
