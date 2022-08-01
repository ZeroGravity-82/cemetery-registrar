<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Coffin
{
    public const CLASS_SHORTCUT = 'COFFIN';
    public const CLASS_LABEL    = 'гроб';

    public function __construct(
        private CoffinSize  $size,
        private CoffinShape $shape,
        private bool        $isNonStandard,
    ) {}

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

    public function size(): CoffinSize
    {
        return $this->size;
    }

    public function shape(): CoffinShape
    {
        return $this->shape;
    }

    public function isNonStandard(): bool
    {
        return $this->isNonStandard;
    }

    public function isEqual(self $coffin): bool
    {
        $isSameSize        = $coffin->size()->isEqual($this->size());
        $isSameShape       = $coffin->shape()->isEqual($this->shape());
        $isSameStandardity = $coffin->isNonStandard() === $this->isNonStandard();

        return $isSameSize && $isSameShape && $isSameStandardity;
    }
}
