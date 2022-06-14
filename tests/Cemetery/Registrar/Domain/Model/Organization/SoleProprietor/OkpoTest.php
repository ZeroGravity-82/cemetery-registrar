<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Okpo;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkpoTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $okpo = new Okpo('0148543122');
        $this->assertSame('0148543122', $okpo->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Okpo('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Okpo('   ');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОКПО должен состоять только из цифр.');
        new Okpo('014854312A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Okpo('014854312');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Okpo('01485431221');
    }

    public function testItFailsWithWrongCheckDigit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОКПО недействителен.');
        new Okpo('0148543123');
    }

    public function testItStringifyable(): void
    {
        $okpo = new Okpo('0148543122');
        $this->assertSame('0148543122', (string) $okpo);
    }

    public function testItComparable(): void
    {
        $okpoA = new Okpo('0148543122');
        $okpoB = new Okpo('0137327072');
        $okpoC = new Okpo('0148543122');

        $this->assertFalse($okpoA->isEqual($okpoB));
        $this->assertTrue($okpoA->isEqual($okpoC));
        $this->assertFalse($okpoB->isEqual($okpoC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОКПО не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ОКПО должен состоять из 10 цифр.');
    }
}
