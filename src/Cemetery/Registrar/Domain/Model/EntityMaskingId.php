<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingId
{
    /**
     * @var EntityId
     */
    protected readonly EntityId $id;

    /**
     * @param EntityId $id
     */
    public function __construct(
        EntityId $id,
    ) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    abstract public function idType(): string;

    /**
     * @return EntityId
     */
    public function id(): EntityId
    {
        return $this->id;
    }

    /**
     * @param self $id
     *
     * @return bool
     */
    public function isEqual(self $id): bool
    {
        $isSameIdClass = \get_class($id->id()) === \get_class($this->id());
        $isSameIdValue = $id->id()->value() === $this->id()->value();

        return $isSameIdClass && $isSameIdValue;
    }
}
