<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNameType extends CustomStringType
{
    /**
     * {@inheritdoc}
     */
    protected string $className = ColumbariumName::class;

    /**
     * {@inheritdoc}
     */
    protected string $typeName  = 'columbarium_name';
}
