<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\AbstractEntityId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityIdTest extends TestCase
{
    protected string $className;

    public function testItSuccessfullyCreated(): void
    {
        /** @var AbstractEntityId $entityId */
        $entityId = new $this->className('777');

        $this->assertSame('777', $entityId->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new $this->className('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new $this->className('   ');
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

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Идентификатор доменной сущности не может иметь пустое значение.');
    }
}
