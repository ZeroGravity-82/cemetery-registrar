<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntity
{
    /**
     * @var \DateTimeImmutable
     */
    protected \DateTimeImmutable $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    protected \DateTimeImmutable $updatedAt;

    /**
     * @var \DateTimeImmutable|null
     */
    protected ?\DateTimeImmutable $removedAt = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * Returns the identity of the domain entity.
     *
     * @return AbstractEntityId
     */
    abstract public function id(): AbstractEntityId;

    /**
     * @return \DateTimeImmutable
     */
    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTimeImmutable|null
     */
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
