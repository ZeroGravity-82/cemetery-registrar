<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\EntityId;
use Cemetery\Registrar\Domain\PolymorphicEntityId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PolymorphicEntityIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $entityType   = 'some_type';
        $entityId     = new EntityId('777');
        $polyEntityId = new PolymorphicEntityId($entityType, $entityId);

        $this->assertSame('some_type', $polyEntityId->getEntityType());
        $this->assertInstanceOf(EntityId::class, $polyEntityId->getEntityId());
        $this->assertSame('777', $polyEntityId->getEntityId()->getValue());
    }

    public function testItComparable(): void
    {
        $entityTypeA = 'some_type';
        $entityTypeB = 'other_type';
        $entityIdA   = new EntityId('777');
        $entityIdB   = new EntityId('888');

        $polyEntityIdA = new PolymorphicEntityId($entityTypeA, $entityIdA);
        $polyEntityIdB = new PolymorphicEntityId($entityTypeA, $entityIdB);
        $polyEntityIdC = new PolymorphicEntityId($entityTypeB, $entityIdA);
        $polyEntityIdD = new PolymorphicEntityId($entityTypeB, $entityIdB);
        $polyEntityIdE = new PolymorphicEntityId($entityTypeA, $entityIdA);

        $this->assertFalse($polyEntityIdA->isEqual($polyEntityIdB));
        $this->assertFalse($polyEntityIdA->isEqual($polyEntityIdC));
        $this->assertFalse($polyEntityIdA->isEqual($polyEntityIdD));
        $this->assertTrue($polyEntityIdA->isEqual($polyEntityIdE));
    }
}
