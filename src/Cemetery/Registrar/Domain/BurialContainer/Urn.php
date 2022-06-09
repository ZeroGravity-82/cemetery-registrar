<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Urn
{
    public const CLASS_SHORTCUT = 'URN';
    public const CLASS_LABEL    = 'урна с прахом';

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::CLASS_LABEL;
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
