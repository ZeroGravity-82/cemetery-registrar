<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\MemorialTree;

use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeNumber;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class MemorialTreeNumberTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $memorialTreeNumber = new MemorialTreeNumber('001');

        $this->assertSame('001', $memorialTreeNumber->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new MemorialTreeNumber('');
    }

    public function testItFailsWithSpaceOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new MemorialTreeNumber('   ');
    }

    public function testItStringifyable(): void
    {
        $memorialTreeNumber = new MemorialTreeNumber('001');

        $this->assertSame('001', (string) $memorialTreeNumber);
    }

    public function testItComparable(): void
    {
        $memorialTreeNumberA = new MemorialTreeNumber('001');
        $memorialTreeNumberB = new MemorialTreeNumber('002');
        $memorialTreeNumberC = new MemorialTreeNumber('001');

        $this->assertFalse($memorialTreeNumberA->isEqual($memorialTreeNumberB));
        $this->assertTrue($memorialTreeNumberA->isEqual($memorialTreeNumberC));
        $this->assertFalse($memorialTreeNumberB->isEqual($memorialTreeNumberC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Номер мемориального дерева не может иметь пустое значение.');
    }
}
