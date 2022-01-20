<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\ORM\NullableEmbeddable;

use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\ORM\NullableEmbeddable\NullableEmbeddableListenerFactory;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\ORM\NullableEmbeddable\NullableEmbeddableListener;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
class ClosureNullatorTest extends TestCase
{
    private NullableEmbeddableListener $listener;

    public function setUp(): void
    {
        $this->listener = NullableEmbeddableListenerFactory::createWithClosureNullator();
    }

    public function testItNullatesUninitializedEmbeddableProperty(): void
    {
        // Prepare object for testing
        $classReflector = new \ReflectionClass(TestNullableEmbeddable::class);
        /** @var TestNullableEmbeddable $uninitializedEmbeddable */
        $uninitializedEmbeddable = $classReflector->newInstanceWithoutConstructor();
        $object                  = new TestEntity($uninitializedEmbeddable);

        $this->listener->addMapping(\get_class($object), 'property');
        $this->listener->postLoad($object);

        // Testing itself
        $this->assertNull($object->getProperty());
    }
}
