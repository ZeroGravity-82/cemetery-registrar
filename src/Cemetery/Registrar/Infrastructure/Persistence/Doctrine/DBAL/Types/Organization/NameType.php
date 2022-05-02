<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization;

use Cemetery\Registrar\Domain\Organization\Name;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class NameType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Name::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName = 'organization_name';
}
