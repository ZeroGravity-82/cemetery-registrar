<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntity
{
    protected \DateTimeImmutable  $createdAt;
    protected \DateTimeImmutable  $updatedAt;
    protected ?\DateTimeImmutable $removedAt = null;

    public function __construct()
    {
        $now             = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    abstract public function id(): AbstractEntityId;

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function removedAt(): ?\DateTimeImmutable
    {
        return $this->removedAt;
    }

    public function refreshUpdatedAtTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function refreshRemovedAtTimestamp(): void
    {
        $this->removedAt = new \DateTimeImmutable();
    }
}
