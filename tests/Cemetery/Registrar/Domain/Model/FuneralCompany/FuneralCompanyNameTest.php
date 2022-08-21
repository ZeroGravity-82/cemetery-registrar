<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyName;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyNameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $name = new FuneralCompanyName('Апостол');
        $this->assertSame('Апостол', $name->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new FuneralCompanyName('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new FuneralCompanyName('   ');
    }

    public function testItStringifyable(): void
    {
        $name = new FuneralCompanyName('Апостол');
        $this->assertSame('Апостол', (string) $name);
    }

    public function testItComparable(): void
    {
        $nameA = new FuneralCompanyName('Апостол');
        $nameB = new FuneralCompanyName('Мемориал');
        $nameC = new FuneralCompanyName('Апостол');

        $this->assertFalse($nameA->isEqual($nameB));
        $this->assertTrue($nameA->isEqual($nameC));
        $this->assertFalse($nameB->isEqual($nameC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Наименование похоронной фирмы не может иметь пустое значение.');
    }
}
