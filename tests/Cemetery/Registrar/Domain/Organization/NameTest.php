<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\Organization\Name;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $name = new Name('ИП Иванов Иван Иванович');
        $this->assertSame('ИП Иванов Иван Иванович', $name->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Name('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Name('   ');
    }

    public function testItStringifyable(): void
    {
        $name = new Name('ИП Иванов Иван Иванович');
        $this->assertSame('ИП Иванов Иван Иванович', (string) $name);
    }

    public function testItComparable(): void
    {
        $nameA = new Name('ИП Иванов Иван Иванович');
        $nameB = new Name('ООО "Рога и копыта"');
        $nameC = new Name('ИП Иванов Иван Иванович');

        $this->assertFalse($nameA->isEqual($nameB));
        $this->assertTrue($nameA->isEqual($nameC));
        $this->assertFalse($nameB->isEqual($nameC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Наименование не может иметь пустое значение.');
    }
}
