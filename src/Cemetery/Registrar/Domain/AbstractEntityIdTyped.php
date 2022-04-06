<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityIdTyped extends AbstractEntityId
{
    private const DELIMITER = ':';

    private string $type;

    /**
     * @param AbstractEntityId $id
     */
    public function __construct(
        AbstractEntityId $id,
    ) {
        parent::__construct($id->getValue());
        $this->type = $this->getShortClassName($id);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getType() . self::DELIMITER . $this->getValue();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param self $id
     *
     * @return bool
     */
    public function isEqual(AbstractEntityId $id): bool
    {
        $isSameClass   = $id instanceof self;
        $isSameIdType  = $id->getType() === $this->getType();
        $isSameIdValue = $id->getValue() === $this->getValue();

        return $isSameClass && $isSameIdType && $isSameIdValue;
    }

    /**
     * @param AbstractEntityId $id
     *
     * @return string
     */
    protected function getShortClassName(AbstractEntityId $id): string
    {
        $parts = \explode('\\', \get_class($id));

        return \end($parts);
    }
}
