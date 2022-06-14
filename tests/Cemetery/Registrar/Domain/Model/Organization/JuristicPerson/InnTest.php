<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Inn;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class InnTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $inn = new Inn('7728168971');
        $this->assertSame('7728168971', $inn->value());
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
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ИНН должен состоять только из цифр.');
        new Inn('772816897A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Inn('772816897');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Inn('77281689711');
    }

    public function testItFailsWithWrongCheckDigit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ИНН недействителен.');
        new Inn('7728168972');
    }

    public function testItStringifyable(): void
    {
        $inn = new Inn('7728168971');
        $this->assertSame('7728168971', (string) $inn);
    }

    public function testItComparable(): void
    {
        $innA = new Inn('7728168971');
        $innB = new Inn('7707083893');
        $innC = new Inn('7728168971');

        $this->assertFalse($innA->isEqual($innB));
        $this->assertTrue($innA->isEqual($innC));
        $this->assertFalse($innB->isEqual($innC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ИНН не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ИНН должен состоять из 10 цифр.');
    }
}
