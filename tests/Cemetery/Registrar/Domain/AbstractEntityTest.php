<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\AbstractEntity;
use Cemetery\Registrar\Domain\AbstractEntityId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityTest extends TestCase
{
    protected AbstractEntity $entity;

    public function testItIsAnEntity(): void
    {
        $this->assertInstanceOf(AbstractEntity::class, $this->entity);
    }

    public function testItReturnsAnId(): void
    {
        $this->assertInstanceOf(AbstractEntityId::class, $this->entity->getId());
    }

    public function testItInitializesTimestamps(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->entity->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->entity->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->entity->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->entity->getUpdatedAt());
        $this->assertNull($this->entity->getDeletedAt());
    }
}
