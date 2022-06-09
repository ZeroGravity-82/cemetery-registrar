<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Coffin
{
    public const CLASS_SHORTCUT = 'COFFIN';
    public const CLASS_LABEL    = 'гроб';

    /**
     * @param CoffinSize  $size
     * @param CoffinShape $shape
     * @param bool        $isNonStandard
     */
    public function __construct(
        private readonly CoffinSize  $size,
        private readonly CoffinShape $shape,
        private readonly bool        $isNonStandard,
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            '%s: размер %d см, форма "%s", %s',
            self::CLASS_LABEL,
            $this->size()->value(),
            $this->shape()->label(),
            $this->isNonStandard() ? 'нестандартный' : 'стандартный'
        );
    }

    /**
     * @return CoffinSize
     */
    public function size(): CoffinSize
    {
        return $this->size;
    }

    /**
     * @return CoffinShape
     */
    public function shape(): CoffinShape
    {
        return $this->shape;
    }

    /**
     * @return bool
     */
    public function isNonStandard(): bool
    {
        return $this->isNonStandard;
    }

    /**
     * @param self $coffin
     *
     * @return bool
     */
    public function isEqual(self $coffin): bool
    {
        $isSameSize        = $coffin->size()->isEqual($this->size());
        $isSameShape       = $coffin->shape()->isEqual($this->shape());
        $isSameStandardity = $coffin->isNonStandard() === $this->isNonStandard();

        return $isSameSize && $isSameShape && $isSameStandardity;
    }
}
