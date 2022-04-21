<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingId
{
    /**
     * @var AbstractEntityId
     */
    protected readonly AbstractEntityId $id;

    /**
     * @param AbstractEntityId $id
     */
    public function __construct(
        AbstractEntityId $id,
    ) {
        $this->id = $id;
    }

    /**
     * @return AbstractEntityId
     */
    public function id(): AbstractEntityId
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
