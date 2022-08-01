<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainerFactory
{
    /**
     * @throws Exception       when the coffin size value is out of valid range
     * @throws \LogicException when the coffin shape is not supported
     */
    public function createForCoffin(?int $size, ?string $shape, ?bool $isNonStandard): BurialContainer
    {
        return new BurialContainer(new Coffin(
            new CoffinSize((int) $size),
            new CoffinShape((string) $shape),
            (bool) $isNonStandard,
        ));
    }

    public function createForUrn(): BurialContainer
    {
        return new BurialContainer(new Urn());
    }
}
