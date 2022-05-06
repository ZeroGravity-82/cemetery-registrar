<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class InnType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Inn::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'juristic_person_inn';
}
