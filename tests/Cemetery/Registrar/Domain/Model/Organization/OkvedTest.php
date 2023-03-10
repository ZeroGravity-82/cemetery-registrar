<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\Okved;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OkvedTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $okved = new Okved('74.82');
        $this->assertSame('74.82', $okved->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Okved('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Okved('   ');
    }

    public function testItFailsWithInvalidFormatA(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Okved('74');
    }

    public function testItFailsWithInvalidFormatB(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Okved('7482.');
    }

    public function testItFailsWithInvalidFormatC(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Okved('74.8A');
    }

    public function testItFailsWithInvalidFormatD(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Okved('74.82.224');
    }

    public function testItFailsWithInvalidFormatE(): void
    {
        $this->expectExceptionForInvalidFormat();
        new Okved('74.82.22.23');
    }

    public function testItStringifyable(): void
    {
        $okved = new Okved('74.82');
        $this->assertSame('74.82', (string) $okved);
    }

    public function testItComparable(): void
    {
        $okvedA = new Okved('74.82');
        $okvedB = new Okved('90.01');
        $okvedC = new Okved('74.82');

        $this->assertFalse($okvedA->isEqual($okvedB));
        $this->assertTrue($okvedA->isEqual($okvedC));
        $this->assertFalse($okvedB->isEqual($okvedC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('?????????? ???? ?????????? ?????????? ???????????? ????????????????.');
    }

    private function expectExceptionForInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('???????????????? ???????????? ??????????.');
    }
}
