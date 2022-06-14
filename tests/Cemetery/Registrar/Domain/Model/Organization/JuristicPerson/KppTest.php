<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Kpp;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class KppTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $kpp = new Kpp('1234AB789');
        $this->assertSame('1234AB789', $kpp->value());

        $kpp = new Kpp('123456789');
        $this->assertSame('123456789', $kpp->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Kpp('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Kpp('   ');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Kpp('1234AB78');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Kpp('1234AB7891');
    }

    public function testItFailsWithInvalidFormatA(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Kpp('1234_B789');
    }

    public function testItFailsWithInvalidFormatB(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Kpp('1234Ф6789');
    }

    public function testItStringifyable(): void
    {
        $kpp = new Kpp('1234AB789');
        $this->assertSame('1234AB789', (string) $kpp);
    }

    public function testItComparable(): void
    {
        $kppA = new Kpp('1234AB789');
        $kppB = new Kpp('123456789');
        $kppC = new Kpp('1234AB789');

        $this->assertFalse($kppA->isEqual($kppB));
        $this->assertTrue($kppA->isEqual($kppC));
        $this->assertFalse($kppB->isEqual($kppC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('КПП не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('КПП должен состоять из 9 символов.');
    }

    private function expectExceptionForInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('КПП имеет неверный формат.');
    }
}
