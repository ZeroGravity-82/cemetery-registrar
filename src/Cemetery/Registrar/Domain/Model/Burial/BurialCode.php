<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialCode
{
    public  const CODE_FORMAT     = '%02d';
    private const CODE_MAX_LENGTH = 9;

    /**
     * @throws Exception when the code is an empty string
     * @throws Exception when the code has non-numeric value
     * @throws Exception when the length of the code is wrong
     * @throws Exception when the code has leading zeros
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    public function __toString(): string
    {
        return \sprintf(self::CODE_FORMAT, $this->value());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqual(self $code): bool
    {
        return $code->value() === $this->value();
    }

    /**
     * @throws Exception when the code is an empty string
     * @throws Exception when the code has non-numeric value
     * @throws Exception when the length of the code is wrong
     * @throws Exception when the code has leading zeros
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
        $this->assertNumeric($value);
        $this->assertValidLength($value);
        $this->assertHasNoLeadingZeros($value);
    }

    /**
     * @throws Exception when the code is an empty string
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new Exception('Код захоронения не может иметь пустое значение.');
        }
    }

    /**
     * @throws Exception when the code has non-numeric value
     */
    private function assertNumeric(string $value): void
    {
        if (!\preg_match('~^\d+$~', $value)) {
            throw new Exception('Код захоронения должен состоять только из цифр.');
        }
    }

    /**
     * @throws Exception when the length of the code is wrong
     */
    private function assertValidLength(string $value): void
    {
        if (\strlen($value) > self::CODE_MAX_LENGTH) {
            throw new Exception(\sprintf(
                'Код захоронения должен состоять не более, чем из %d цифр.',
                self::CODE_MAX_LENGTH
            ));
        }
    }

    /**
     * @throws Exception when the code has leading zeros
     */
    private function assertHasNoLeadingZeros(string $value): void
    {
        $significantDigitCount = \strlen((string) \abs((int) $value));
        if (\strlen($value) !== $significantDigitCount) {
            throw new Exception('Код захоронения не должен содержать ведущие нули.');
        }
    }
}
