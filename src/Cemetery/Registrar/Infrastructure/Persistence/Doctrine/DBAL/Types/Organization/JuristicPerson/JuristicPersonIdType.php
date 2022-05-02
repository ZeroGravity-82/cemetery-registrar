<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonIdType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = JuristicPersonId::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'juristic_person_id';
}
