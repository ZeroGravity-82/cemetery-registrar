<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialContainerId;
use Cemetery\Registrar\Domain\Burial\BurialContainerType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialContainerType = new BurialContainerType(BurialContainerType::COFFIN);
        $burialContainerId   = new BurialContainerId('777', $burialContainerType);
        $this->assertSame('777', $burialContainerId->getValue());
        $this->assertSame($burialContainerType, $burialContainerId->getType());
    }

    public function testItStringifyable(): void
    {
        $burialContainerType = new BurialContainerType(BurialContainerType::COFFIN);
        $burialContainerId   = new BurialContainerId('777', $burialContainerType);
        $this->assertSame(BurialContainerType::COFFIN . '.' . '777', (string) $burialContainerId);
    }

    public function testItComparable(): void
    {
        $burialContainerIdA = new BurialContainerId('777', new BurialContainerType(BurialContainerType::COFFIN));
        $burialContainerIdB = new BurialContainerId('777', new BurialContainerType(BurialContainerType::URN));
        $burialContainerIdC = new BurialContainerId('888', new BurialContainerType(BurialContainerType::COFFIN));
        $burialContainerIdD = new BurialContainerId('777', new BurialContainerType(BurialContainerType::COFFIN));

        $this->assertFalse($burialContainerIdA->isEqual($burialContainerIdB));
        $this->assertFalse($burialContainerIdA->isEqual($burialContainerIdC));
        $this->assertTrue($burialContainerIdA->isEqual($burialContainerIdD));
        $this->assertFalse($burialContainerIdB->isEqual($burialContainerIdC));
        $this->assertFalse($burialContainerIdB->isEqual($burialContainerIdD));
        $this->assertFalse($burialContainerIdC->isEqual($burialContainerIdD));
    }
}
