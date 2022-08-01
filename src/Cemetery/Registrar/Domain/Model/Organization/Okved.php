<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Okved
{
    /**
     * @throws Exception when the OKVED is empty
     * @throws Exception when the OKVED has invalid format
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

    public function isEqual(self $okved): bool
    {
        return $okved->value() === $this->value();
    }

    /**
     * @throws Exception when the OKVED is empty
     * @throws Exception when the OKVED has invalid format
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidFormat($value);
    }

    /**
     * @throws Exception when the OKVED is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('ОКВЭД не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the OKVED has invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match('~^\d{2}\.\d{2}(\.\d{1,2})?$~', $value)) {
            throw new Exception('Неверный формат ОКВЭД.');
        }
    }
}
