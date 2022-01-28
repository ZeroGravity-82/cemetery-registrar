<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyType
{
    public const SOLE_PROPRIETOR = 'sole_proprietor';
    public const JURISTIC_PERSON = 'juristic_person';

    /**
     * @param string $value
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    /**
     * @return self
     */
    public static function soleProprietor(): self
    {
        return new self(self::SOLE_PROPRIETOR);
    }

    /**
     * @return self
     */
    public static function juristicPerson(): self
    {
        return new self(self::JURISTIC_PERSON);
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
     * @return bool
     */
    public function isSoleProprietor(): bool
    {
        return $this->value === self::SOLE_PROPRIETOR;
    }

    /**
     * @return bool
     */
    public function isJuristicPerson(): bool
    {
        return $this->value === self::JURISTIC_PERSON;
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the funeral company type is not supported
     */
    private function assertValidValue(string $value): void
    {
        $supportedFuneralCompanyTypes = [self::SOLE_PROPRIETOR, self::JURISTIC_PERSON];
        if (!\in_array($value, $supportedFuneralCompanyTypes)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported funeral company type "%s", expected to be one of %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedFuneralCompanyTypes))
            ));
        }
    }
}