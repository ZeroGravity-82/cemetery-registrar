<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonIdType extends AbstractCustomStringType
{
    protected string $className = NaturalPersonId::class;
    protected string $typeName  = 'natural_person_id';
}
