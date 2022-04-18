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
        return \json_encode(['type' => $this->getIdType(), 'value' => $this->getId()->getValue()]);
    }

    /**
     * @return AbstractEntityId
     */
    public function getId(): AbstractEntityId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdType(): string
    {
        $parts = \explode('\\', \get_class($this->getId()));

        return \end($parts);
    }

    /**
     * @param self $id
     *
     * @return bool
     */
    public function isEqual(self $id): bool
    {
        $isSameIdValue = $id->getId()->getValue() === $this->getId()->getValue();
        $isSameIdType  = \get_class($id->getId()) === \get_class($this->getId());

        return $isSameIdValue && $isSameIdType;
    }
}
