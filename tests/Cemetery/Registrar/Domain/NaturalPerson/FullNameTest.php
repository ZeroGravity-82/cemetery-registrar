<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullNameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $fullName = new FullName('Иванов Иван Иванович');
        $this->assertSame('Иванов Иван Иванович', $fullName->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new FullName('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new FullName('   ');
    }

    public function testItStringifyable(): void
    {
        $fullName = new FullName('Иванов Иван Иванович');
        $this->assertSame('Иванов Иван Иванович', (string) $fullName);
    }

    public function testItComparable(): void
    {
        $fullNameA = new FullName('Иванов Иван Иванович');
        $fullNameB = new FullName('Петров Пётр Петрович');
        $fullNameC = new FullName('Иванов Иван Иванович');

        $this->assertFalse($fullNameA->isEqual($fullNameB));
        $this->assertTrue($fullNameA->isEqual($fullNameC));
        $this->assertFalse($fullNameB->isEqual($fullNameC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ФИО не может иметь пустое значение.');
    }
}
