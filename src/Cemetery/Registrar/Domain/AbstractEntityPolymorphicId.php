<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityPolymorphicId
{
    /**
     * @var AbstractEntityId
     */
    protected readonly AbstractEntityId $id;

    public function __construct(
        AbstractEntityId $id,
    ) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \json_encode(['type' => $this->idType(), 'value' => $this->id()->value()]);
    }

    /**
     * @return AbstractEntityId
     */
    public function id(): AbstractEntityId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function idType(): string
    {
        $parts = \explode('\\', \get_class($this->id()));

        return \end($parts);
    }

    /**
     * @param self $id
     *
     * @return bool
     */
    public function isEqual(self $id): bool
    {
        $isSameIdValue = $id->id()->value() === $this->id()->value();
        $isSameIdType  = \get_class($id->id()) === \get_class($this->id());

        return $isSameIdValue && $isSameIdType;
    }
}
