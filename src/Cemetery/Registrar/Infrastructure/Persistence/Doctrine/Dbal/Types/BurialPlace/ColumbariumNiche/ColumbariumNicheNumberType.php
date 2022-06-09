<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheNumberType extends CustomStringType
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
