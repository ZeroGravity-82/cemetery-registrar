<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\ORM\NullableEmbeddable;

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
     * @param EvaluatorInterface $evaluator
     * @param NullatorInterface  $nullator
     */
    public function __construct(
        private readonly EvaluatorInterface $evaluator,
        private readonly NullatorInterface  $nullator,
    ) {}

    public function addMapping(string $entity, string $propertyPath)
    {
        if (empty($this->propertyMap[$entity])) {
            $this->propertyMap[$entity] = [];
        }

        $this->propertyMap[$entity][] = $propertyPath;
    }

    public function postLoad($object)
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
