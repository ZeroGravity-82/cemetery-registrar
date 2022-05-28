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
    public function testItHasValidCodeFormatConstant(): void
    {
        $this->assertSame('%02d', BurialCode::CODE_FORMAT);
    }

    public function testItSuccessfullyCreated(): void
    {
        $burialCode = new BurialCode('1');
        $this->assertSame('1', $burialCode->value());

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
        new BurialCode('1027.0011');
    }

    public function testItFailsWithNonNumericValueC(): void
    {
        $this->expectExceptionForNonNumericValue();
        new BurialCode('1027 0011');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new BurialCode('1027700112');
    }

    public function testItFailsWithLeadingZerosA(): void
    {
        $this->expectExceptionForLeadingZeros();
        new BurialCode('010');
    }

    public function testItStringifyable(): void
    {
        $burialCode = new BurialCode('1');
        $this->assertSame('01', (string) $burialCode);

        $burialCode = new BurialCode('8');
        $this->assertSame('08', (string) $burialCode);

        $burialCode = new BurialCode('11');
        $this->assertSame('11', (string) $burialCode);

        $burialCode = new BurialCode('1001');
        $this->assertSame('1001', (string) $burialCode);
    }

    public function testItComparable(): void
    {
        $burialCodeA = new BurialCode('1');
        $burialCodeB = new BurialCode('1002');
        $burialCodeC = new BurialCode('1');

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
        $this->expectExceptionMessage('Код захоронения должен состоять не более, чем из 9 цифр.');
    }

    private function expectExceptionForLeadingZeros(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Код захоронения не должен содержать ведущие нули.');
    }
}
