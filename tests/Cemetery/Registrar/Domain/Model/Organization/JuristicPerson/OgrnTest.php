<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Ogrn;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OgrnTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $ogrn = new Ogrn('1027700132195');
        $this->assertSame('1027700132195', $ogrn->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Ogrn('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Ogrn('   ');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОГРН должен состоять только из цифр.');
        new Ogrn('102770013219A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Ogrn('102770013219');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Ogrn('10277001321951');
    }

    public function testItFailsWithWrongCheckDigit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОГРН недействителен.');
        new Ogrn('1027700132194');
    }

    public function testItStringifyable(): void
    {
        $ogrn = new Ogrn('1027700132195');
        $this->assertSame('1027700132195', (string) $ogrn);
    }

    public function testItComparable(): void
    {
        $ogrnA = new Ogrn('1027700132195');
        $ogrnB = new Ogrn('1027700067328');
        $ogrnC = new Ogrn('1027700132195');

        $this->assertFalse($ogrnA->isEqual($ogrnB));
        $this->assertTrue($ogrnA->isEqual($ogrnC));
        $this->assertFalse($ogrnB->isEqual($ogrnC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОГРН не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОГРН должен состоять из 13 цифр.');
    }
}
