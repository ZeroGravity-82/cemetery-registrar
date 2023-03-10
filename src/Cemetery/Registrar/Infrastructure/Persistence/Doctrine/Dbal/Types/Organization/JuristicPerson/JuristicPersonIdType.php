<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonIdType extends AbstractCustomStringType
{
    protected string $className = JuristicPersonId::class;
    protected string $typeName  = 'juristic_person_id';
}
