<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Okpo;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkpoTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $okpo = new Okpo('23584736');
        $this->assertSame('23584736', $okpo->value());
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
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОКПО должен состоять только из цифр.');
        new Okpo('2358473A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Okpo('2358473');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new Okpo('235847361');
    }

    public function testItFailsWithWrongCheckDigit(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОКПО недействителен.');
        new Okpo('23584737');
    }

    public function testItStringifyable(): void
    {
        $okpo = new Okpo('23584736');
        $this->assertSame('23584736', (string) $okpo);
    }

    public function testItComparable(): void
    {
        $okpoA = new Okpo('23584736');
        $okpoB = new Okpo('09610444');
        $okpoC = new Okpo('23584736');

        $this->assertFalse($okpoA->isEqual($okpoB));
        $this->assertTrue($okpoA->isEqual($okpoC));
        $this->assertFalse($okpoB->isEqual($okpoC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОКПО не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('ОКПО должен состоять из 8 цифр.');
    }
}
