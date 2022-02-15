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
        $Name = new Name('ИП Иванов Иван Иванович');
        $this->assertSame($Name->getValue(), 'ИП Иванов Иван Иванович');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Наименование не может иметь пустое значение.');
        new Name('');
    }

    public function testItStringifyable(): void
    {
        $Name = new Name('ИП Иванов Иван Иванович');
        $this->assertSame('ИП Иванов Иван Иванович', (string) $Name);
    }

    public function testItComparable(): void
    {
        $NameA = new Name('ИП Иванов Иван Иванович');
        $NameB = new Name('ООО "Рога и копыта"');
        $NameC = new Name('ИП Иванов Иван Иванович');

        $this->assertFalse($NameA->isEqual($NameB));
        $this->assertTrue($NameA->isEqual($NameC));
        $this->assertFalse($NameB->isEqual($NameC));
    }
}
