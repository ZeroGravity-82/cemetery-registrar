<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialType
{
    public const COFFIN_IN_GRAVE_SITE      = 'гробом в могилу';
    public const URN_IN_GRAVE_SITE         = 'урной в могилу';
    public const URN_IN_COLUMBARIUM_NICHE  = 'урной в колумбарную нишу';
    public const ASHES_UNDER_MEMORIAL_TREE = 'прахом под деревом';

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
    public static function coffinInGraveSite(): self
    {
        return new self(self::COFFIN_IN_GRAVE_SITE);
    }

    /**
     * @return self
     */
    public static function urnInGraveSite(): self
    {
        return new self(self::URN_IN_GRAVE_SITE);
    }

    /**
     * @return self
     */
    public static function urnInColumbariumNiche(): self
    {
        return new self(self::URN_IN_COLUMBARIUM_NICHE);
    }

    /**
     * @return self
     */
    public static function ashesUnderMemorialTree(): self
    {
        return new self(self::ASHES_UNDER_MEMORIAL_TREE);
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
    public function isCoffinInGraveSite(): bool
    {
        return $this->value === self::COFFIN_IN_GRAVE_SITE;
    }

    /**
     * @return bool
     */
    public function isUrnInGraveSite(): bool
    {
        return $this->value === self::URN_IN_GRAVE_SITE;
    }

    /**
     * @return bool
     */
    public function isUrnInColumbariumNiche(): bool
    {
        return $this->value === self::URN_IN_COLUMBARIUM_NICHE;
    }

    /**
     * @return bool
     */
    public function isAshesUnderMemorialTree(): bool
    {
        return $this->value === self::ASHES_UNDER_MEMORIAL_TREE;
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the burial type is not supported
     */
    private function assertValidValue(string $value): void
    {
        $supportedBurialTypes = [
            self::COFFIN_IN_GRAVE_SITE,
            self::URN_IN_GRAVE_SITE,
            self::URN_IN_COLUMBARIUM_NICHE,
            self::ASHES_UNDER_MEMORIAL_TREE,
        ];
        if (!\in_array($value, $supportedBurialTypes)) {
            throw new \InvalidArgumentException(\sprintf(
                'Неподдерживаемый тип захоронения "%s", должен быть один из %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedBurialTypes))
            ));
        }
    }
}
