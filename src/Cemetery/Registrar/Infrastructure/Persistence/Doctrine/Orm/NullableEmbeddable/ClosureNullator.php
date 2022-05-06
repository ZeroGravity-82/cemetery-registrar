<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\NullableEmbeddable;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
final class ClosureNullator implements Nullator
{
    public function setNull($object, $property)
    {
        $nullator = \Closure::bind(function ($property) {
            $this->{$property} = null;
        }, $object, $object);

        $nullator($property);
    }
}
