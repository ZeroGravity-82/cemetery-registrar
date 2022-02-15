<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Ogrnip
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
     * @param self $fullName
     *
     * @return bool
     */
    public function isEqual(self $fullName): bool
    {
        return $fullName->getValue() === $this->getValue();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertValidCheckDigit($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OGRNIP is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('ОГРНИП не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OGRNIP has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException('ОГРНИП должен состоять только из цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the OGRNIP is not equal to 15
     */
    private function assertValidLength(string $value): void
    {
        if (\strlen($value) !== 15) {
            throw new \InvalidArgumentException('ОГРНИП должен состоять из 15 цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the OGRNIP contains an incorrect check digit
     */
    private function assertValidCheckDigit(string $value): void
    {
        // TODO implement the following:
        // Контрольная цифра. Она равна младшему разряду остатка от деления числа, состоящего из первых 12 цифр, на 11 (для юрлиц) или 14-значного числа на 13 (для ИП). Если остаток больше 9, контрольная цифра равна последней цифре остатка.
    }
}
