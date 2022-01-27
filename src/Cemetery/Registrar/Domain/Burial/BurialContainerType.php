<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialContainerType
{
    public const COFFIN = 'coffin';
    public const URN    = 'urn';

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
    public static function coffin(): self
    {
        return new self(self::COFFIN);
    }

    /**
     * @return self
     */
    public static function urn(): self
    {
        return new self(self::URN);
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
    public function isCoffin(): bool
    {
        return $this->value === self::COFFIN;
    }

    /**
     * @return bool
     */
    public function isUrn(): bool
    {
        return $this->value === self::URN;
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the burial container type is not supported
     */
    private function assertValidValue(string $value): void
    {
        $supportedBurialContainerTypes = [self::COFFIN, self::URN];
        if (!\in_array($value, $supportedBurialContainerTypes)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported burial container type "%s", expected to be one of %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedBurialContainerTypes))
            ));
        }
    }
}
