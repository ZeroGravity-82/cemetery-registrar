<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CoffinShape
{
    public const TRAPEZOID             = 'trapezoid';
    public const GREEK_WITH_HANDLES    = 'greek_with_handles';
    public const GREEK_WITHOUT_HANDLES = 'greek_without_handles';
    public const AMERICAN              = 'american';

    private const DISPLAY_NAMES = [
        self::TRAPEZOID             => 'трапеция',
        self::GREEK_WITH_HANDLES    => 'грек (с ручками)',
        self::GREEK_WITHOUT_HANDLES => 'грек (без ручек)',
        self::AMERICAN              => 'американец',
    ];

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
    public static function trapezoid(): self
    {
        return new self(self::TRAPEZOID);
    }

    /**
     * @return self
     */
    public static function greekWithHandles(): self
    {
        return new self(self::GREEK_WITH_HANDLES);
    }

    /**
     * @return self
     */
    public static function greekWithoutHandles(): self
    {
        return new self(self::GREEK_WITHOUT_HANDLES);
    }

    /**
     * @return self
     */
    public static function american(): self
    {
        return new self(self::AMERICAN);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->displayName();
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
    public function displayName(): string
    {
        return self::DISPLAY_NAMES[$this->value()];
    }

    /**
     * @param CoffinShape $coffinShape
     *
     * @return bool
     */
    public function isEqual(self $coffinShape): bool
    {
        return $coffinShape->value() === $this->value();
    }

    /**
     * @return bool
     */
    public function isTrapezoid(): bool
    {
        return $this->value() === self::TRAPEZOID;
    }

    /**
     * @return bool
     */
    public function isGreekWithHandles(): bool
    {
        return $this->value() === self::GREEK_WITH_HANDLES;
    }

    /**
     * @return bool
     */
    public function isGreekWithoutHandles(): bool
    {
        return $this->value() === self::GREEK_WITHOUT_HANDLES;
    }

    /**
     * @return bool
     */
    public function isAmerican(): bool
    {
        return $this->value() === self::AMERICAN;
    }

    /**
     * @param string $value
     *
     * @throws \RuntimeException when the coffin shape is not supported
     */
    private function assertValidValue(string $value): void
    {
        $supportedCoffinShapes = [
            self::TRAPEZOID,
            self::GREEK_WITH_HANDLES,
            self::GREEK_WITHOUT_HANDLES,
            self::AMERICAN,
        ];
        if (!\in_array($value, $supportedCoffinShapes)) {
            throw new \RuntimeException(\sprintf(
                'Неподдерживаемая форма гроба "%s", должна быть одна из %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedCoffinShapes))
            ));
        }
    }
}
