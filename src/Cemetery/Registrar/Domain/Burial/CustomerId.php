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
     * @param string $value
     * @param string $type
     */
    public function __construct(
        protected string $value,
        private   string $type,
    ) {
        parent::__construct($value);
        $this->assertValidType($type);
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
        $isValidClass = $id instanceof self;
        $isSameType   = $id->getType()  === $this->getType();
        $isSameValue  = $id->getValue() === $this->getValue();

        return $isValidClass && $isSameType && $isSameValue;
    }

    /**
     * @param string $type
     */
    protected function assertValidType(string $type): void
    {
        $this->assertNotEmpty($type);

    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the ID is an empty string
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('Domain entity ID cannot be empty string.');
        }
    }
}
