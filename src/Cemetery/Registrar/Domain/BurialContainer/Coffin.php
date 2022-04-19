<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\ValueObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Coffin extends ValueObject implements BurialContainer
{
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
            'гроб: размер %d см, форма "%s", %s',
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