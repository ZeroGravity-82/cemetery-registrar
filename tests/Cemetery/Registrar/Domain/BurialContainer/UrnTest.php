<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\Urn;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class UrnTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(Urn::class, new Urn());
    }

    public function testItStringifyable(): void
    {
        $urn = new Urn();
        $this->assertSame('урна с прахом', (string) $urn);
    }

    public function testItComparable(): void
    {
        $urnA = new Urn();
        $urnB = new Urn();
        $this->assertTrue($urnA->isEqual($urnB));
    }
}
