<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CustomerId
{
    /**
     * @param string $type
     * @param string $value
     */
    public function __construct(
        private string $type,
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getValue();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param self $id
     *
     * @return bool
     */
    public function isEqual(self $id): bool
    {
        $isSameType  = $id->getType()  === $this->getType();
        $isSameValue = $id->getValue() === $this->getValue();

        return $isSameType && $isSameValue;
    }

    /**
     * @param string $value
     */
    protected function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
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
