<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\Bik;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BikTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $bik = new Bik('045004774');
        $this->assertSame($bik->getValue(), '045004774');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('БИК не может иметь пустое значение.');
        new Bik('');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('БИК должен состоять только из цифр.');
        new Bik('04500477A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('БИК должен состоять из 9 цифр.');
        new Bik('04500477');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('БИК должен состоять из 9 цифр.');
        new Bik('0450047741');
    }

    public function testItStringifyable(): void
    {
        $bik = new Bik('045004774');
        $this->assertSame('045004774', (string) $bik);
    }

    public function testItComparable(): void
    {
        $bikA = new Bik('045004774');
        $bikB = new Bik('044106001');
        $bikC = new Bik('045004774');

        $this->assertFalse($bikA->isEqual($bikB));
        $this->assertTrue($bikA->isEqual($bikC));
        $this->assertFalse($bikB->isEqual($bikC));
    }
}
