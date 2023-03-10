<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class InnTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $inn = new Inn('772208786091');
        $this->assertSame('772208786091', $inn->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Inn('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Inn('   ');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ИНН должен состоять только из цифр.');
        new Inn('77220878609A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Inn('77220878609');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Inn('7722087860911');
    }

    public function testItFailsWithWrongCheckDigit(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ИНН недействителен.');
        new Inn('772208786093');
    }

    public function testItStringifyable(): void
    {
        $inn = new Inn('772208786091');
        $this->assertSame('772208786091', (string) $inn);
    }

    public function testItComparable(): void
    {
        $innA = new Inn('772208786091');
        $innB = new Inn('391600743661');
        $innC = new Inn('772208786091');

        $this->assertFalse($innA->isEqual($innB));
        $this->assertTrue($innA->isEqual($innC));
        $this->assertFalse($innB->isEqual($innC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ИНН не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ИНН должен состоять из 12 цифр.');
    }
}
