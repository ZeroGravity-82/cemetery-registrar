<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingId
{
    public function __construct(
        protected EntityId $id,
    ) {}

    abstract public function idType(): string;

    public function id(): EntityId
    {
        return $this->id;
    }

    public function isEqual(self $id): bool
    {
        $isSameIdClass = \get_class($id->id()) === \get_class($this->id());
        $isSameIdValue = $id->id()->value() === $this->id()->value();

        return $isSameIdClass && $isSameIdValue;
    }
}
