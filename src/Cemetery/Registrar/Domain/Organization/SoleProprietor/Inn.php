<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Inn
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
     * @param self $inn
     *
     * @return bool
     */
    public function isEqual(self $inn): bool
    {
        return $inn->getValue() === $this->getValue();
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
     * @throws \InvalidArgumentException when the INN is empty
     */
    private function assertNotEmpty(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('ИНН не может иметь пустое значение.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the INN has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\is_numeric($value)) {
            throw new \InvalidArgumentException('ИНН должен состоять только из цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the length of the INN is not equal to 12
     */
    private function assertValidLength(string $value): void
    {
        if (\strlen($value) !== 12) {
            throw new \InvalidArgumentException('ИНН должен состоять из 12 цифр.');
        }
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the INN contains an incorrect check digit
     */
    private function assertValidCheckDigit(string $value): void
    {
        // TODO implement the following:
        // Проверку ИНН проводят путем вычисления одного контрольного числа для 10-значных ИНН и двух контрольных чисел для 12-значных ИНН. Коэффициенты для вычисления первого контрольного числа n1 для 10-значного ИНН:
        // 2, 4, 10, 3, 5, 9, 4, 6, 8.
        // Коэффициенты для вычисления первого контрольного числа n1 для 12-значного ИНН:
        // 3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8.
        // Коэффициенты для вычисления второго контрольного числа n2 для 12-значного ИНН:
        // 7, 2, 4, 10, 3, 5, 9, 4, 6, 8.
        // Шаг 1. Контрольное число n1 вычисляется как остаток от деления на 11 суммы из цифр номера (по порядку слева направо), умноженных на соответствующие (приведенные выше) коэффициенты. Если в остатке получается 10, то n1 = 0. Полученное контрольное число n1 должно совпадать с последней цифрой ИНН (как 10-значного, так и 12-значного).

    }
}
