<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialContainerFactory
{
    /**
     * @param int|null    $size
     * @param string|null $shape
     * @param bool|null   $isNonStandard
     *
     * @return BurialContainer
     */
    public function createForCoffin(?int $size, ?string $shape, ?bool $isNonStandard): BurialContainer
    {
        return new BurialContainer(new Coffin(
            new CoffinSize((int) $size),
            new CoffinShape((string) $shape),
            (bool) $isNonStandard,
        ));
    }

    /**
     * @return BurialContainer
     */
    public function createForUrn(): BurialContainer
    {
        return new BurialContainer(new Urn());
    }
}
