<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Urn
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'урна с прахом';
    }

    /**
     * @param Urn $urn
     *
     * @return bool
     */
    public function isEqual(self $urn): bool
    {
        return true;
    }
}
