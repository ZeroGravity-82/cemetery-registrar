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

    public const LABELS = [
        self::COFFIN_IN_GRAVE_SITE      => 'гробом в могилу',
        self::URN_IN_GRAVE_SITE         => 'урной в могилу',
        self::URN_IN_COLUMBARIUM_NICHE  => 'урной в колумбарную нишу',
        self::ASHES_UNDER_MEMORIAL_TREE => 'прахом под деревом',
    ];

    /**
     * @throws \LogicException when the burial type is not supported
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    public static function coffinInGraveSite(): self
    {
        return new self(self::COFFIN_IN_GRAVE_SITE);
    }

    public static function urnInGraveSite(): self
    {
        return new self(self::URN_IN_GRAVE_SITE);
    }

    public static function urnInColumbariumNiche(): self
    {
        return new self(self::URN_IN_COLUMBARIUM_NICHE);
    }

    public static function ashesUnderMemorialTree(): self
    {
        return new self(self::ASHES_UNDER_MEMORIAL_TREE);
    }

    public function __toString(): string
    {
        return $this->label();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function label(): string
    {
        return self::LABELS[$this->value()];
    }

    public function isEqual(self $type): bool
    {
        return $type->value() === $this->value();
    }

    public function isCoffinInGraveSite(): bool
    {
        return $this->value === self::COFFIN_IN_GRAVE_SITE;
    }

    public function isUrnInGraveSite(): bool
    {
        return $this->value === self::URN_IN_GRAVE_SITE;
    }

    public function isUrnInColumbariumNiche(): bool
    {
        return $this->value === self::URN_IN_COLUMBARIUM_NICHE;
    }

    public function isAshesUnderMemorialTree(): bool
    {
        return $this->value === self::ASHES_UNDER_MEMORIAL_TREE;
    }

    /**
     * @throws \LogicException when the burial type is not supported
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
            throw new \LogicException(\sprintf(
                'Неподдерживаемый тип захоронения "%s", должен быть один из %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedBurialTypes))
            ));
        }
    }
}
