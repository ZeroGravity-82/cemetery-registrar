<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\Organization\Name;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $Name = new Name('IP Ivanov Ivan Ivanovich');
        $this->assertSame($Name->getValue(), 'IP Ivanov Ivan Ivanovich');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Full name value cannot be empty.');
        new Name('');
    }

    public function testItStringifyable(): void
    {
        $Name = new Name('IP Ivanov Ivan Ivanovich');
        $this->assertSame('IP Ivanov Ivan Ivanovich', (string) $Name);
    }

    public function testItComparable(): void
    {
        $NameA = new Name('IP Ivanov Ivan Ivanovich');
        $NameB = new Name('IP Petrov Petr Petrovich');
        $NameC = new Name('IP Ivanov Ivan Ivanovich');

        $this->assertFalse($NameA->isEqual($NameB));
        $this->assertTrue($NameA->isEqual($NameC));
        $this->assertFalse($NameB->isEqual($NameC));
    }
}
