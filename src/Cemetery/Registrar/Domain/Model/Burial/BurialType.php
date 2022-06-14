<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialType
{
    public const COFFIN_IN_GRAVE_SITE      = 'COFFIN_IN_GRAVE_SITE';
    public const URN_IN_GRAVE_SITE         = 'URN_IN_GRAVE_SITE';
    public const URN_IN_COLUMBARIUM_NICHE  = 'URN_IN_COLUMBARIUM_NICHE';
    public const ASHES_UNDER_MEMORIAL_TREE = 'ASHES_UNDER_MEMORIAL_TREE';

    public const TYPE_LABELS = [
        self::COFFIN_IN_GRAVE_SITE      => 'гробом в могилу',
        self::URN_IN_GRAVE_SITE         => 'урной в могилу',
        self::URN_IN_COLUMBARIUM_NICHE  => 'урной в колумбарную нишу',
        self::ASHES_UNDER_MEMORIAL_TREE => 'прахом под деревом',
    ];

    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
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
        return $this->label();
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return self::TYPE_LABELS[$this->value()];
    }


    /**
     * @param self $type
     *
     * @return bool
     */
    public function isEqual(self $type): bool
    {
        return $type->value() === $this->value();
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
            throw new \RuntimeException(\sprintf(
                'Неподдерживаемый тип захоронения "%s", должен быть один из %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedBurialTypes))
            ));
        }
    }
}
