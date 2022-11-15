<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model;

use Cemetery\Registrar\Domain\Model\AbstractEntity;
use Cemetery\Registrar\Domain\Model\AbstractEntityId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityTest extends TestCase
{
    protected AbstractEntity $entity;

    public function testItReturnsId(): void
    {
        $this->assertInstanceOf(AbstractEntityId::class, $this->entity->id());
    }

    public function testItInitializesTimestamps(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->entity->createdAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->entity->createdAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->entity->updatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->entity->updatedAt());
        $this->assertNull($this->entity->removedAt());
    }
}
