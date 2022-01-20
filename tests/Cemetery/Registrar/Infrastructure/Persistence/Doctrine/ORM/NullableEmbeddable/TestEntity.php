<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\ORM\NullableEmbeddable;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
final class TestEntity
{
    private ?TestNullableEmbeddable $property;

    public function __construct(TestNullableEmbeddable $embeddable)
    {
        $this->property = $embeddable;
    }

    public function getProperty(): ?TestNullableEmbeddable
    {
        return $this->property;
    }
}
