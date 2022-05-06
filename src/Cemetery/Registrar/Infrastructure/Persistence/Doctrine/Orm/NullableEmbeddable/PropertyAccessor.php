<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\NullableEmbeddable;

use Symfony\Component\PropertyAccess\PropertyAccessor as DefaultPropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
final class PropertyAccessor implements EvaluatorInterface, NullatorInterface
{
    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
    ) {}

    /**
     * @return self
     */
    public static function createWithDefault(): self
    {
        return new self(new DefaultPropertyAccessor());
    }

    public function isNull($object, $property): bool
    {
        $isNull         = true;
        $embeddable     = $this->propertyAccessor->getValue($object, $property);
        $classReflector = new \ReflectionClass($embeddable);
        foreach ($classReflector->getProperties() as $property) {
            $property->setAccessible(true);
            if ($property->isInitialized($embeddable) && $property->getValue($embeddable) !== null) {
                $isNull = false;
                break;
            }
        }

        return $isNull;
    }

    public function setNull($object, $property)
    {
        $this->propertyAccessor->setValue($object, $property, null);
    }
}
