<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractEntityId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialContainerId extends AbstractEntityId
{
    private const DELIMITER = '.';

    /**
     * @param string              $value
     * @param BurialContainerType $type
     */
    public function __construct(
        protected string              $value,
        private   BurialContainerType $type,
    ) {
        parent::__construct($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getType() . self::DELIMITER . $this->getValue();
    }

    /**
     * @return BurialContainerType
     */
    public function getType(): BurialContainerType
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
        $isSameClass               = $id instanceof self;
        $isSameBurialContainerType = $id->getType()->isEqual($this->getType());
        $isSameIdValue             = $id->getValue() === $this->getValue();

        return $isSameClass && $isSameBurialContainerType && $isSameIdValue;
    }
}
