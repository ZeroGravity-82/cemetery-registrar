<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockName;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockNameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $columbariumName = new CemeteryBlockName('воинский квартал');

        $this->assertSame('воинский квартал', $columbariumName->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new CemeteryBlockName('');
    }

    public function testItFailsWithSpaceOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new CemeteryBlockName('   ');
    }

    public function testItStringifyable(): void
    {
        $cemeteryBlockName = new CemeteryBlockName('воинский квартал');

        $this->assertSame('воинский квартал', (string) $cemeteryBlockName);
    }

    public function testItComparable(): void
    {
        $cemeteryBlockNameA = new CemeteryBlockName('воинский квартал');
        $cemeteryBlockNameB = new CemeteryBlockName('мусульманский квартал');
        $cemeteryBlockNameC = new CemeteryBlockName('воинский квартал');

        $this->assertFalse($cemeteryBlockNameA->isEqual($cemeteryBlockNameB));
        $this->assertTrue($cemeteryBlockNameA->isEqual($cemeteryBlockNameC));
        $this->assertFalse($cemeteryBlockNameB->isEqual($cemeteryBlockNameC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Название квартала не может иметь пустое значение.');
    }
}
