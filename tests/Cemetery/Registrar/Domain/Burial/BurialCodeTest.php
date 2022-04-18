<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialCode;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCodeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialCode = new BurialCode('AAA');

        $this->assertSame('AAA', $burialCode->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new BurialCode('');
    }

    public function testItFailsWithSpaceOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new BurialCode('   ');
    }

    public function testItStringifyable(): void
    {
        $burialCode = new BurialCode('AAA');

        $this->assertSame('AAA', (string) $burialCode);
    }

    public function testItComparable(): void
    {
        $burialCodeA = new BurialCode('AAA');
        $burialCodeB = new BurialCode('BBB');
        $burialCodeC = new BurialCode('AAA');

        $this->assertFalse($burialCodeA->isEqual($burialCodeB));
        $this->assertTrue($burialCodeA->isEqual($burialCodeC));
        $this->assertFalse($burialCodeB->isEqual($burialCodeC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Код захоронения не может иметь пустое значение.');
    }
}
