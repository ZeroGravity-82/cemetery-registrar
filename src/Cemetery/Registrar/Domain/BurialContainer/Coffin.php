<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Coffin
{
    /**
     * @param CoffinSize  $size
     * @param CoffinShape $shape
     * @param bool        $isNonStandard
     */
    public function __construct(
        private CoffinSize  $size,
        private CoffinShape $shape,
        private bool        $isNonStandard,
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \sprintf(
            'Гроб: размер %d см, форма "%s", %s',
            $this->size()->getValue(),
            $this->shape()->getDisplayName(),
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
