<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\NullableEmbeddable;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
final class NullableEmbeddableListener
{
    /**
     * @var string[][]
     */
    private array $propertyMap = [];

    /**
     * @param Evaluator $evaluator
     * @param NullatorInterface  $nullator
     */
    public function __construct(
        private readonly Evaluator $evaluator,
        private readonly NullatorInterface  $nullator,
    ) {}

    public function addMapping(string $entity, string $propertyPath): void
    {
        if (empty($this->propertyMap[$entity])) {
            $this->propertyMap[$entity] = [];
        }

        $this->propertyMap[$entity][] = $propertyPath;
    }

    public function postLoad($object): void
    {
        $entity = \get_class($object);
        if (empty($this->propertyMap[$entity])) {
            return;
        }

        $entries = $this->propertyMap[$entity];
        foreach ($entries as $property) {
            if ($this->evaluator->isNull($object, $property)) {
                $this->nullator->setNull($object, $property);
            }
        }
    }
}
