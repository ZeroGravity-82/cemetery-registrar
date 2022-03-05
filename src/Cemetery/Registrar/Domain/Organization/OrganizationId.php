<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

use Cemetery\Registrar\Domain\AbstractEntityId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class OrganizationId extends AbstractEntityId
{
    private const DELIMITER = '.';

    /**
     * @param string           $value
     * @param OrganizationType $type
     */
    public function __construct(
        protected string           $value,
        private   OrganizationType $type,
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
     * @return OrganizationType
     */
    public function getType(): OrganizationType
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
        $isSameClass            = $id instanceof self;
        $isSameOrganizationType = $id->getType()->isEqual($this->getType());
        $isSameIdValue          = $id->getValue() === $this->getValue();

        return $isSameClass && $isSameOrganizationType && $isSameIdValue;
    }
}
