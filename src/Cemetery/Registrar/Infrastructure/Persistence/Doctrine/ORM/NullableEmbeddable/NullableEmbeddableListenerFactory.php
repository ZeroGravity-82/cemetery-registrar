<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\ORM\NullableEmbeddable;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
final class NullableEmbeddableListenerFactory
{
    public static function createWithPropertyAccessor(): NullableEmbeddableListener
    {
        $default = PropertyAccessor::createWithDefault();

        return new NullableEmbeddableListener($default, $default);
    }

    public static function createWithClosureNullator(): NullableEmbeddableListener
    {
        $evaluator = PropertyAccessor::createWithDefault();
        $nullator  = new ClosureNullator();

        return new NullableEmbeddableListener($evaluator, $nullator);
    }
}
