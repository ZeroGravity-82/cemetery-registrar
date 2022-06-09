<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomIntegerType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RowInColumbariumType extends CustomIntegerType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = RowInColumbarium::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'row_in_columbarium';
}
