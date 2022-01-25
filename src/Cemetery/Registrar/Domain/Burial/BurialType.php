<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialType
{
    public const COFFIN_IN_GRAVE    = 'coffin_in_grave';
    public const URN_IN_GRAVE       = 'urn_in_grave';
    public const URN_IN_COLUMBARIUM = 'urn_in_columbarium';
    public const ASHES_UNDER_TREE   = 'ashes_under_tree';

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
    public static function coffinInGrave(): self
    {
        return new self(self::COFFIN_IN_GRAVE);
    }

    /**
     * @return self
     */
    public static function urnInGrave(): self
    {
        return new self(self::URN_IN_GRAVE);
    }

    /**
     * @return self
     */
    public static function urnInColumbarium(): self
    {
        return new self(self::URN_IN_COLUMBARIUM);
    }

    /**
     * @return self
     */
    public static function ashesUnderTree(): self
    {
        return new self(self::ASHES_UNDER_TREE);
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
    public function isCoffinInGrave(): bool
    {
        return $this->value === self::COFFIN_IN_GRAVE;
    }

    /**
     * @return bool
     */
    public function isUrnInGrave(): bool
    {
        return $this->value === self::URN_IN_GRAVE;
    }

    /**
     * @return bool
     */
    public function isUrnInColumbarium(): bool
    {
        return $this->value === self::URN_IN_COLUMBARIUM;
    }

    /**
     * @return bool
     */
    public function isAshesUnderTree(): bool
    {
        return $this->value === self::ASHES_UNDER_TREE;
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the burial type is not supported
     */
    private function assertValidValue(string $value): void
    {
        $supportedBurialTypes = [
            self::COFFIN_IN_GRAVE,
            self::URN_IN_GRAVE,
            self::URN_IN_COLUMBARIUM,
            self::ASHES_UNDER_TREE,
        ];
        if (!\in_array($value, $supportedBurialTypes)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported burial type "%s", expected to be one of %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedBurialTypes))
            ));
        }
    }
}
