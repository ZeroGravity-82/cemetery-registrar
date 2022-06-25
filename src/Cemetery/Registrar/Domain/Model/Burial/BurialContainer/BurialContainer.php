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
    /**
     * @param Coffin|Urn $container
     */
    public function __construct(
        private readonly Coffin|Urn $container,
    ) {}

    /**
     * @return Coffin|Urn
     */
    public function container(): Coffin|Urn
    {
        return $this->container;
    }

    /**
     * @param self $container
     *
     * @return bool
     */
    public function isEqual(self $container): bool
    {
        $isSameContainerClass = \get_class($container->container()) === \get_class($this->container());
        $isSameContainerValue = $isSameContainerClass && $container->container()->isEqual($this->container());

        return $isSameContainerClass && $isSameContainerValue;
    }
}