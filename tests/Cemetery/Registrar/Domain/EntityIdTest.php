<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\EntityId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EntityIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $entityId = new EntityId('777');

        $this->assertSame('777', $entityId->getValue());
    }

    public function testItFailsWithEmptyIdString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Domain entity ID cannot be empty string.');
        new EntityId('');
    }

    public function testItStringifyable(): void
    {
        $entityId = new EntityId('777');

        $this->assertSame('777', (string) $entityId);
    }

    public function testItComparable(): void
    {
        $entityIdA = new EntityId('777');
        $entityIdB = new EntityId('888');
        $entityIdC = new EntityId('777');

        $this->assertFalse($entityIdA->isEqual($entityIdB));
        $this->assertTrue($entityIdA->isEqual($entityIdC));
        $this->assertFalse($entityIdB->isEqual($entityIdC));
    }
}
