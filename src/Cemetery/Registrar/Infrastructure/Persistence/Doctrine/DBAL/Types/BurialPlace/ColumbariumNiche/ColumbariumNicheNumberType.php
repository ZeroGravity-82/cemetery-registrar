<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class ColumbariumNicheNumberType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = ColumbariumNicheNumber::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'columbarium_niche_number';
}
