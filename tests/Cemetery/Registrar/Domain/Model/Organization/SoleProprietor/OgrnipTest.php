<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Ogrnip;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OgrnipTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $ogrnip = new Ogrnip('315547600024379');
        $this->assertSame('315547600024379', $ogrnip->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Ogrnip('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Ogrnip('   ');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОГРНИП должен состоять только из цифр.');
        new Ogrnip('31554760002437A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Ogrnip('31554760002437');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Ogrnip('3155476000243791');
    }

    public function testItFailsWithWrongCheckDigit(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОГРНИП недействителен.');
        new Ogrnip('315547600024378');
    }

    public function testItStringifyable(): void
    {
        $ogrnip = new Ogrnip('315547600024379');
        $this->assertSame('315547600024379', (string) $ogrnip);
    }

    public function testItComparable(): void
    {
        $ogrnipA = new Ogrnip('315547600024379');
        $ogrnipB = new Ogrnip('313547607000096');
        $ogrnipC = new Ogrnip('315547600024379');

        $this->assertFalse($ogrnipA->isEqual($ogrnipB));
        $this->assertTrue($ogrnipA->isEqual($ogrnipC));
        $this->assertFalse($ogrnipB->isEqual($ogrnipC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОГРНИП не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОГРНИП должен состоять из 15 цифр.');
    }
}
