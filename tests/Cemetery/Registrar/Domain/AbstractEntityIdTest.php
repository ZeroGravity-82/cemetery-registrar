<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityIdTest extends TestCase
{
    protected string $className;

    public function testItSuccessfullyCreated(): void
    {
        $entityId = new $this->className('777');

        $this->assertSame('777', $entityId->getValue());
    }

    public function testItFailsWithEmptyIdString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Domain entity ID cannot be empty string.');
        new $this->className('');
    }

    public function testItStringifyable(): void
    {
        $entityId = new $this->className('777');

        $this->assertSame('777', (string) $entityId);
    }

    public function testItComparable(): void
    {
        $entityIdA = new $this->className('777');
        $entityIdB = new $this->className('888');
        $entityIdC = new $this->className('777');

        $this->assertFalse($entityIdA->isEqual($entityIdB));
        $this->assertTrue($entityIdA->isEqual($entityIdC));
        $this->assertFalse($entityIdB->isEqual($entityIdC));
    }
}