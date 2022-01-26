<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialPlaceType
{
    public const GRAVE_SITE        = 'grave_site';
    public const COLUMBARIUM_NICHE = 'columbarium_niche';
    public const MEMORIAL_TREE     = 'memorial_tree';

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
    public static function graveSite(): self
    {
        return new self(self::GRAVE_SITE);
    }

    /**
     * @return self
     */
    public static function columbariumNiche(): self
    {
        return new self(self::COLUMBARIUM_NICHE);
    }

    /**
     * @return self
     */
    public static function memorialTree(): self
    {
        return new self(self::MEMORIAL_TREE);
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
    public function isGraveSite(): bool
    {
        return $this->value === self::GRAVE_SITE;
    }

    /**
     * @return bool
     */
    public function isColumbariumNiche(): bool
    {
        return $this->value === self::COLUMBARIUM_NICHE;
    }

    /**
     * @return bool
     */
    public function isMemorialTree(): bool
    {
        return $this->value === self::MEMORIAL_TREE;
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the burial place type is not supported
     */
    private function assertValidValue(string $value): void
    {
        $supportedBurialPlaceTypes = [self::GRAVE_SITE, self::COLUMBARIUM_NICHE, self::MEMORIAL_TREE];
        if (!\in_array($value, $supportedBurialPlaceTypes)) {
            throw new \InvalidArgumentException(\sprintf(
                'Unsupported burial place type "%s", expected to be one of %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedBurialPlaceTypes))
            ));
        }
    }
}
