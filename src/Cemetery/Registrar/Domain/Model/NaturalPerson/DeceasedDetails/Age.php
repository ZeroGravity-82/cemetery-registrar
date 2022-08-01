<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Age
{
    private const MAX_AGE = 125;

    /**
     * @throws Exception when the age is negative
     * @throws Exception when the age has too much value
     */
    public function __construct(
        private int $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @throws Exception when the target date is before the date of birth
     */
    public static function fromDates(\DateTimeImmutable $bornAt, ?\DateTimeImmutable $targetDate = null): self
    {
        self::assertValidDates($bornAt, $targetDate);
        $now = new \DateTimeImmutable();

        return new self($bornAt->diff($targetDate ?? $now)->y);
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isEqual(self $type): bool
    {
        return $type->value() === $this->value();
    }

    /**
     * @throws Exception when the age is negative
     * @throws Exception when the age has too much value
     */
    private function assertValidValue(int $value): void
    {
        $this->assertNotNegative($value);
        $this->assertNotTooMuch($value);
    }

    /**
     * @throws Exception when the age is negative
     */
    private function assertNotNegative(int $value): void
    {
        if ($value < 0) {
            throw new Exception('Возраст не может иметь отрицательное значение.');
        }
    }

    /**
     * @throws Exception when the age has too much value
     */
    private function assertNotTooMuch(int $value): void
    {
        if ($value > self::MAX_AGE) {
            throw new Exception(\sprintf('Возраст не может превышать %d лет.', self::MAX_AGE));
        }
    }

    /**
     * @throws Exception when the target date is before the date of birth
     */
    private static function assertValidDates(\DateTimeImmutable $bornAt, ?\DateTimeImmutable $targetDate): void
    {
        if ($targetDate && $targetDate < $bornAt) {
            throw new Exception('Конечная дата не может предшествовать дате рождения.');
        }
    }
}
