<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization;

use Cemetery\Registrar\Domain\Organization\Okved;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class OkvedType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = Okved::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'okved';
}
