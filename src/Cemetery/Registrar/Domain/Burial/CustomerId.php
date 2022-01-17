<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractEntityId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CustomerId extends AbstractEntityId
{
    private const DELIMITER = '.';

    /**
     * @param string       $value
     * @param CustomerType $type
     */
    public function __construct(
        protected string       $value,
        private   CustomerType $type,
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
     * @return CustomerType
     */
    public function getType(): CustomerType
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
        $isSameClass        = $id instanceof self;
        $isSameCustomerType = $id->getType()->isEqual($this->getType());
        $isSameIdValue      = $id->getValue() === $this->getValue();

        return $isSameClass && $isSameCustomerType && $isSameIdValue;
    }
}
