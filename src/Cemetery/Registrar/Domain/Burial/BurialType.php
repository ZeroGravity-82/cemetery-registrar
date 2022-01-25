<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialType
{
    public const COFFIN_IN_GROUND          = 'coffin_in_ground';
    public const URN_IN_GROUND             = 'urn_in_ground';
    public const URN_IN_OPEN_COLUMBARIUM   = 'urn_in_open_columbarium';
    public const URN_IN_CLOSED_COLUMBARIUM = 'urn_in_closed_columbarium';
    public const URN_IN_SARCOPHAGUS        = 'urn_in_sarcophagus';
    public const ASHES_UNDER_TREE          = 'ashes_under_tree';

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
    public static function coffinInGround(): self
    {
        return new self(self::COFFIN_IN_GROUND);
    }

    /**
     * @return self
     */
    public static function urnInGround(): self
    {
        return new self(self::URN_IN_GROUND);
    }

    /**
     * @return self
     */
    public static function urnInOpenColumbarium(): self
    {
        return new self(self::URN_IN_OPEN_COLUMBARIUM);
    }

    /**
     * @return self
     */
    public static function urnInClosedColumbarium(): self
    {
        return new self(self::URN_IN_CLOSED_COLUMBARIUM);
    }

    /**
     * @return self
     */
    public static function urnInSarcophagus(): self
    {
        return new self(self::URN_IN_SARCOPHAGUS);
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
    public function isCoffinInGround(): bool
    {
        return $this->value === self::COFFIN_IN_GROUND;
    }

    /**
     * @return bool
     */
    public function isUrnInGround(): bool
    {
        return $this->value === self::URN_IN_GROUND;
    }

    /**
     * @return bool
     */
    public function isUrnInOpenColumbarium(): bool
    {
        return $this->value === self::URN_IN_OPEN_COLUMBARIUM;
    }

    /**
     * @return bool
     */
    public function isUrnInClosedColumbarium(): bool
    {
        return $this->value === self::URN_IN_CLOSED_COLUMBARIUM;
    }

    /**
     * @return bool
     */
    public function isUrnInSarcophagus(): bool
    {
        return $this->value === self::URN_IN_SARCOPHAGUS;
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
            self::COFFIN_IN_GROUND,
            self::URN_IN_GROUND,
            self::URN_IN_OPEN_COLUMBARIUM,
            self::URN_IN_CLOSED_COLUMBARIUM,
            self::URN_IN_SARCOPHAGUS,
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
