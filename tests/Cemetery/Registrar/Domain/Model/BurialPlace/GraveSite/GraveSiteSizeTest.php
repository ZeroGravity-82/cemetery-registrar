<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteSizeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $graveSiteSize = new GraveSiteSize('2.5');
        $this->assertSame('2.5', $graveSiteSize->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Размер участка не может иметь отрицательное значение.');
        new GraveSiteSize('-1.5');
    }

    public function testItFailsWithInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Неверный формат размера участка.');
        new GraveSiteSize('2.5A');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new GraveSiteSize('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new GraveSiteSize('   ');
    }

    public function testItStringifyable(): void
    {
        $graveSiteSize = new GraveSiteSize('3.125');
        $this->assertSame('3.125', (string) $graveSiteSize);
    }

    public function testItComparable(): void
    {
        $graveSiteSizeA = new GraveSiteSize('2.5');
        $graveSiteSizeB = new GraveSiteSize('3.125');
        $graveSiteSizeC = new GraveSiteSize('2.5');

        $this->assertFalse($graveSiteSizeA->isEqual($graveSiteSizeB));
        $this->assertTrue($graveSiteSizeA->isEqual($graveSiteSizeC));
        $this->assertFalse($graveSiteSizeB->isEqual($graveSiteSizeC));
    }

    public function testItChecksFormat(): void
    {
        $this->assertTrue(GraveSiteSize::isValidFormat('2.5'));
        $this->assertTrue(GraveSiteSize::isValidFormat('3.125'));
        $this->assertFalse(GraveSiteSize::isValidFormat('-2.5'));
        $this->assertFalse(GraveSiteSize::isValidFormat('2.7A'));
        $this->assertFalse(GraveSiteSize::isValidFormat('.75'));
        $this->assertFalse(GraveSiteSize::isValidFormat('2..5'));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Размер участка не может иметь пустое значение.');
    }
}
