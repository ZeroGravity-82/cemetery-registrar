<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FullNameType extends CustomStringType
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
