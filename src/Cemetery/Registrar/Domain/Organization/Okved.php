<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Okved
{
    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value();
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @param self $okved
     *
     * @return bool
     */
    public function isEqual(self $okved): bool
    {
        return $okved->value() === $this->value();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertValidFormat($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OKVED is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('ОКВЭД не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OKVED has invalid format
     */
    private function assertValidFormat(string $value): void
    {
        if (!\preg_match('~^\d{2}\.\d{2}(\.\d{1,2})?$~', $value)) {
            throw new \InvalidArgumentException('ОКВЭД имеет неверный формат.');
        }
    }
}
