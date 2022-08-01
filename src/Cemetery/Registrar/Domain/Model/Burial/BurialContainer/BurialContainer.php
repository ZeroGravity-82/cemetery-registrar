<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial\BurialContainer;

/**
 * Wrapper class for value objects that are burial containers.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialContainer
{
    public function __construct(
        private Coffin|Urn $container,
    ) {}

    public function container(): Coffin|Urn
    {
        return $this->container;
    }

    public function isEqual(self $container): bool
    {
        $isSameContainerClass = \get_class($container->container()) === \get_class($this->container());
        $isSameContainerValue = $isSameContainerClass && $container->container()->isEqual($this->container());

        return $isSameContainerClass && $isSameContainerValue;
    }
}
