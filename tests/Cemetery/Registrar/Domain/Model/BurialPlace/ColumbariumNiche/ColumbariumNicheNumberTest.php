<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\Model\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheNumberTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $columbariumNicheNumber = new ColumbariumNicheNumber('001');

        $this->assertSame('001', $columbariumNicheNumber->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new ColumbariumNicheNumber('');
    }

    public function testItFailsWithSpaceOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new ColumbariumNicheNumber('   ');
    }

    public function testItStringifyable(): void
    {
        $columbariumNicheNumber = new ColumbariumNicheNumber('001');

        $this->assertSame('001', (string) $columbariumNicheNumber);
    }

    public function testItComparable(): void
    {
        $columbariumNicheNumberA = new ColumbariumNicheNumber('001');
        $columbariumNicheNumberB = new ColumbariumNicheNumber('002');
        $columbariumNicheNumberC = new ColumbariumNicheNumber('001');

        $this->assertFalse($columbariumNicheNumberA->isEqual($columbariumNicheNumberB));
        $this->assertTrue($columbariumNicheNumberA->isEqual($columbariumNicheNumberC));
        $this->assertFalse($columbariumNicheNumberB->isEqual($columbariumNicheNumberC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Номер колумбарной ниши не может иметь пустое значение.');
    }
}
