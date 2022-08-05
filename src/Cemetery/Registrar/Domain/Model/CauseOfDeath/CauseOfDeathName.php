<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathName
{
    /**
     * @throws Exception when the cause of death is an empty string
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $type): bool
    {
        return $type->value() === $this->value();
    }

    /**
     * @throws Exception when the cause of death is an empty string
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @throws Exception when the cause of death is an empty string
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Причина смерти не может иметь пустое наименование.');
        }
    }
}
