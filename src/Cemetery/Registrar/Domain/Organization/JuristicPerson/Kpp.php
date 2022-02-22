<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Kpp
{
    private const KPP_LENGTH = 9;

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
     * @param self $kpp
     *
     * @return bool
     */
    public function isEqual(self $kpp): bool
    {
        return $kpp->getValue() === $this->getValue();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidLength($value);
        $this->assertValidFormat($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the KPP is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('КПП не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the INN is wrong
     */
    private function assertValidLength(string $value): void
    {
        if (\mb_strlen($value) !== self::KPP_LENGTH) {
            throw new \InvalidArgumentException(\sprintf('КПП должен состоять из %d символов.', self::KPP_LENGTH));
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the KPP has invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match('~^\d{4}[0-9A-Z]{2}\d{3}$~', $value)) {
            throw new \InvalidArgumentException('Неверный формат КПП.');
        }
    }
}
