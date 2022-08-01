<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Kpp
{
    private const KPP_LENGTH = 9;

    /**
     * @throws Exception when the KPP is empty
     * @throws Exception when the length of the KPP is wrong
     * @throws Exception when the KPP has invalid format
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

    public function isEqual(self $kpp): bool
    {
        return $kpp->value() === $this->value();
    }

    /**
     * @throws Exception when the KPP is empty
     * @throws Exception when the length of the KPP is wrong
     * @throws Exception when the KPP has invalid format
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidLength($value);
        $this->assertValidFormat($value);
    }

    /**
     * @throws Exception when the KPP is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('КПП не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the length of the KPP is wrong
     */
    private function assertValidLength(string $value): void
    {
        if (\mb_strlen($value) !== self::KPP_LENGTH) {
            throw new Exception(\sprintf('КПП должен состоять из %d символов.', self::KPP_LENGTH));
        }
    }

    /**
     * @throws Exception when the KPP has invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match('~^\d{4}[\dA-Z]{2}\d{3}$~', $value)) {
            throw new Exception('Неверный формат КПП.');
        }
    }
}
