<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

/**
 * @todo Rework to read the list of all possible causes from some repository
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CauseOfDeath
{
    /**
     * @param string $value
     */
    public function __construct(
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param self $type
     *
     * @return bool
     */
    public function isEqual(self $type): bool
    {
        return $type->getValue() === $this->getValue();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the cause of death is an empty string
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('Cause of death cannot be empty string.');
        }
    }
}