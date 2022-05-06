<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\NullableEmbeddable;

use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 * @see    https://github.com/tarifhaus/doctrine-nullable-embeddable
 */
interface NullatorInterface
{
    public function setNull($object, $property);
}
