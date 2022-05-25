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
        $burialCode = new BurialCode('01');
        $this->assertSame('01', $burialCode->value());

        $burialCode = new BurialCode('10');
        $this->assertSame('10', $burialCode->value());

        $burialCode = new BurialCode('1001');
        $this->assertSame('1001', $burialCode->value());
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

    public function testItFailsWithNonNumericValueA(): void
    {
        $this->expectExceptionForNonNumericValue();
        new BurialCode('10277001A');
    }

    public function testItFailsWithNonNumericValueB(): void
    {
        $this->expectExceptionForNonNumericValue();
        new BurialCode('1027-0011');
    }

    public function testItFailsWithNonNumericValueC(): void
    {
        $this->expectExceptionForNonNumericValue();
        new BurialCode('1027 0011');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new BurialCode('1');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new BurialCode('1027700112');
    }

    public function testItStringifyable(): void
    {
        $burialCode = new BurialCode('1001');

        $this->assertSame('1001', (string) $burialCode);
    }

    public function testItComparable(): void
    {
        $burialCodeA = new BurialCode('01');
        $burialCodeB = new BurialCode('1002');
        $burialCodeC = new BurialCode('01');

        $this->assertFalse($burialCodeA->isEqual($burialCodeB));
        $this->assertTrue($burialCodeA->isEqual($burialCodeC));
        $this->assertFalse($burialCodeB->isEqual($burialCodeC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Код захоронения не может иметь пустое значение.');
    }

    private function expectExceptionForNonNumericValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Код захоронения должен состоять только из цифр.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Код захоронения должен иметь длину от 2 до 9 цифр.');
    }
}
