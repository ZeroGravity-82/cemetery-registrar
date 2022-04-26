<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumName;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $columbariumName = new ColumbariumName('западный колумбарий');

        $this->assertSame('западный колумбарий', $columbariumName->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new ColumbariumName('');
    }

    public function testItFailsWithSpaceOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new ColumbariumName('   ');
    }

    public function testItStringifyable(): void
    {
        $columbariumName = new ColumbariumName('восточный колумбарий');

        $this->assertSame('восточный колумбарий', (string) $columbariumName);
    }

    public function testItComparable(): void
    {
        $columbariumNameA = new ColumbariumName('западный колумбарий');
        $columbariumNameB = new ColumbariumName('восточный колумбарий');
        $columbariumNameC = new ColumbariumName('западный колумбарий');

        $this->assertFalse($columbariumNameA->isEqual($columbariumNameB));
        $this->assertTrue($columbariumNameA->isEqual($columbariumNameC));
        $this->assertFalse($columbariumNameB->isEqual($columbariumNameC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Название колумбария не может иметь пустое значение.');
    }
}
