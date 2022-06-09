<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = NaturalPersonId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'natural_person_id';
}
