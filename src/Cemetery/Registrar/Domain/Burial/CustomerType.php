<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CustomerType
{
    public const NATURAL_PERSON  = 'физическое лицо';
    public const SOLE_PROPRIETOR = 'индивидуальный предприниматель';
    public const JURISTIC_PERSON = 'юридическое лицо';

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
    public static function naturalPerson(): self
    {
        return new self(self::NATURAL_PERSON);
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
    public function isNaturalPerson(): bool
    {
        return $this->value === self::NATURAL_PERSON;
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
     * @throws \InvalidArgumentException when the customer type is not supported
     */
    private function assertValidValue(string $value): void
    {
        $supportedCustomerTypes = [self::NATURAL_PERSON, self::SOLE_PROPRIETOR, self::JURISTIC_PERSON];
        if (!\in_array($value, $supportedCustomerTypes)) {
            throw new \InvalidArgumentException(\sprintf(
                'Неподдерживаемый тип заказчика захоронения "%s", должен быть один из %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedCustomerTypes))
            ));
        }
    }
}
