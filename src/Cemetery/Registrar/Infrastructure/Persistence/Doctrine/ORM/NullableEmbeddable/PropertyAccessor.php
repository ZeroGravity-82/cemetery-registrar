<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\ORM\NullableEmbeddable;

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
        private PropertyAccessorInterface $propertyAccessor,
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
        $embeddable = $this->propertyAccessor->getValue($object, $property);
        if ($embeddable instanceof NullableEmbeddableInterface) {
            return $embeddable->isNull();
        }

        return false;
    }

    public function setNull(&$object, $property)
    {
        $this->propertyAccessor->setValue($object, $property, null);
    }
}
