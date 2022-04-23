<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\RowInBlock;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RowInBlockTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $rowInBlock = new RowInBlock(10);
        $this->assertSame(10, $rowInBlock->value());
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
