<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShape
{
    public const TRAPEZOID             = 'TRAPEZOID';
    public const GREEK_WITH_HANDLES    = 'GREEK_WITH_HANDLES';
    public const GREEK_WITHOUT_HANDLES = 'GREEK_WITHOUT_HANDLES';
    public const AMERICAN              = 'AMERICAN';

    public const LABELS = [
        self::TRAPEZOID             => 'трапеция',
        self::GREEK_WITH_HANDLES    => 'грек (с ручками)',
        self::GREEK_WITHOUT_HANDLES => 'грек (без ручек)',
        self::AMERICAN              => 'американец',
    ];

    /**
     * @throws \LogicException when the coffin shape is not supported
     */
    public function __construct(
        private string $value,
    ) {
        $this->assertValidValue($value);
    }

    public static function trapezoid(): self
    {
        return new self(self::TRAPEZOID);
    }

    public static function greekWithHandles(): self
    {
        return new self(self::GREEK_WITH_HANDLES);
    }

    public static function greekWithoutHandles(): self
    {
        return new self(self::GREEK_WITHOUT_HANDLES);
    }

    public static function american(): self
    {
        return new self(self::AMERICAN);
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

    public function isEqual(self $coffinShape): bool
    {
        return $coffinShape->value() === $this->value();
    }

    public function isTrapezoid(): bool
    {
        return $this->value() === self::TRAPEZOID;
    }

    public function isGreekWithHandles(): bool
    {
        return $this->value() === self::GREEK_WITH_HANDLES;
    }

    public function isGreekWithoutHandles(): bool
    {
        return $this->value() === self::GREEK_WITHOUT_HANDLES;
    }

    public function isAmerican(): bool
    {
        return $this->value() === self::AMERICAN;
    }

    /**
     * @throws \LogicException when the coffin shape is not supported
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
            throw new \LogicException(\sprintf(
                'Неподдерживаемая форма гроба "%s", должна быть одна из %s.',
                $value,
                \implode(', ', \array_map(function ($item) { return \sprintf('"%s"', $item); }, $supportedCoffinShapes))
            ));
        }
    }
}
