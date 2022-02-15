<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\FullName;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullNameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $fullName = new FullName('IP Ivanov Ivan Ivanovich');
        $this->assertSame($fullName->getValue(), 'IP Ivanov Ivan Ivanovich');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Full name value cannot be empty.');
        new FullName('');
    }

    public function testItStringifyable(): void
    {
        $fullName = new FullName('IP Ivanov Ivan Ivanovich');
        $this->assertSame('IP Ivanov Ivan Ivanovich', (string) $fullName);
    }

    public function testItComparable(): void
    {
        $fullNameA = new FullName('IP Ivanov Ivan Ivanovich');
        $fullNameB = new FullName('IP Petrov Petr Petrovich');
        $fullNameC = new FullName('IP Ivanov Ivan Ivanovich');

        $this->assertFalse($fullNameA->isEqual($fullNameB));
        $this->assertTrue($fullNameA->isEqual($fullNameC));
        $this->assertFalse($fullNameB->isEqual($fullNameC));
    }
}
