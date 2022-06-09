<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\PlaceOfBirth;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PlaceOfBirthType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = PlaceOfBirth::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'place_of_birth';
}
