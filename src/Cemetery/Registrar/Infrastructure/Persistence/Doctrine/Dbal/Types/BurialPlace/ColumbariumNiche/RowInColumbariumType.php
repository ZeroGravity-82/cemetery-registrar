<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomIntegerType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RowInColumbariumType extends CustomIntegerType
{
    protected string $className = RowInColumbarium::class;
    protected string $typeName  = 'row_in_columbarium';
}
