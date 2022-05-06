<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\NullableEmbeddable;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
final class TestNullableEmbeddable
{
    /**
     * @param bool $foo
     */
    public function __construct(
        private bool $foo,
    ) {}

    public function getFoo(): bool
    {
        return $this->foo;
    }
}
