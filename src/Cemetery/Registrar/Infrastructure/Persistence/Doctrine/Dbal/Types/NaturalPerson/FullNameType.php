<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FullNameType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = FullName::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'full_name';
}
