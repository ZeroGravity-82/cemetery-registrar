<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ValueObject
{
    /**
     * Returns the short name of the class for the value object.
     *
     * @return string
     */
    public function className(): string
    {
        $parts = \explode('\\', \get_class($this));

        return \end($parts);
    }
}
