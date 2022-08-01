<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial\BurialContainer;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Urn
{
    public const CLASS_SHORTCUT = 'URN';
    public const CLASS_LABEL    = 'урна с прахом';

    public function __toString(): string
    {
        return self::CLASS_LABEL;
    }

    public function isEqual(self $urn): bool
    {
        return true;
    }
}
